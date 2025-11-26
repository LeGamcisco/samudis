import sys
import time
import psycopg2
import psycopg2.extras
from pymodbus.client import ModbusTcpClient
from pymodbus.constants import Defaults
from datetime import datetime, timedelta

class BackupADC:
    def __init__(self) -> None:
        self.db = psycopg2.connect(host="localhost",database="egateway_v2",user="postgres",password="r2h2s12*")
        self.cursor = self.db.cursor(cursor_factory = psycopg2.extras.RealDictCursor)
    def connect(self):
        try:
            self.db = psycopg2.connect(host="localhost",database="egateway_v2",user="postgres",password="r2h2s12*")
            self.cursor = self.db.cursor(cursor_factory = psycopg2.extras.RealDictCursor)
        except Exception as e:
            print("DB Not Connect!")

    def getParameters(self):
        try:
            self.cursor.execute("select parameter_id, ain, ip_analyzer, formula from parameters where ip_analyzer is not null and ain is not null")
            return self.cursor.fetchall()
        except Exception as e:
            print(e)
            return []
        
    def isExist(self,parameterId):
        try:
            now = datetime.now()
            lastOnline = now - timedelta(minutes=3) # Selisih 3 menit
            lastOnline = lastOnline.strftime("%Y-%m-%d %H:%M:%S")
            #where = "is_das_log = 0 and is_direct_plc = 0 and parameter_id ="+str(parameterId)+" and to_char(xtimestamp, 'YYYY-MM-DD HH24:MI:SS') >= '"+lastOnline+"'"
            where = "is_direct_plc = 0 and parameter_id ="+str(parameterId)+" and to_char(xtimestamp, 'YYYY-MM-DD HH24:MI:SS') >= '"+lastOnline+"'"
            self.cursor.execute("select id, xtimestamp from measurement_logs where "+where+" ORDER BY id DESC")
            log = self.cursor.fetchone()
            return False if log == None else True
        except Exception as e:
            print(e)
            return False
        
    def getValueFromPLC(self,ipAddress, AIn,formula):
        try:
            Defaults.Timeout=3
            client = ModbusTcpClient(ipAddress, port=502)  # Specify the port.
            connection = client.connect()
            if(connection):
                request = client.read_holding_registers(0, 8)
                v_data = request.registers
                ma = (2.44144E-4*v_data[AIn]) + 4
                client.close()
                return [eval(formula),ma]
            return [-111,0]
        except Exception as e:
            print("Modbus TCP Error :", e)
            return [-222,0]
            
    def insertLogs(self,parameterId, conc, mA):
        try: 
            now = datetime.now()
            now = now.strftime("%Y-%m-%d %H:%M:%S")
            
            self.updateValLog(parameterId, conc, now)
            query = "INSERT INTO measurement_logs(parameter_id, value, voltage, is_direct_plc, xtimestamp) \
            VALUES ('{}','{}','{}','1', '{}')".format(parameterId,conc,mA, now)
            self.cursor.execute(query)
            self.db.commit()
        except Exception as e:
            print("Insert Measurement Logs Error: ",e)
            return False
        
    def updateValLog(self,parameterId, conc, xtimestamp):
        try: 
            now = datetime.now()
            now = now.strftime("%Y-%m-%d %H:%M:%S")
            self.cursor.execute("SELECT count(*) as count_parameter from value_logs where parameter_id = {}".format(parameterId))
            value = self.cursor.fetchone()
            if(value['count_parameter'] < 1):
                query = "INSERT INTO value_logs(parameter_id, measured, corrective, xtimestamp) values ('{}','{}','0','{}')".format(parameterId,conc, xtimestamp)
                self.cursor.execute(query)
                self.db.commit()
                return True
            else:
                query = "UPDATE value_logs SET measured = '{}', corrective = '0', xtimestamp = '{}' WHERE parameter_id = '{}'".format(conc, xtimestamp, parameterId)
                self.cursor.execute(query)
                self.db.commit()
                return True
            
        except Exception as e:
            print("Update Value Error: ",e)
            return False
        
    def main(self):
        #while(True):
        try:
            parameters = self.getParameters()
            for parameter in parameters:
                if self.isExist(parameter['parameter_id']) == False:
                    value = self.getValueFromPLC(parameter['ip_analyzer'],parameter['ain'], parameter['formula'])
                    self.insertLogs(parameter['parameter_id'], value[0], value[1])
            #time.sleep(3)
        except Exception as e:
            print("Backup ADC Error: ",e)
BackupADC().main()