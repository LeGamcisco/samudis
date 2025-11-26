from __future__ import print_function
from datetime import datetime, timedelta
import psycopg2
import psycopg2.extras
from pymodbus.constants import Endian
from pymodbus.payload import BinaryPayloadDecoder
from pymodbus.client import ModbusTcpClient
import math
from random import randint
def get_value(ip_address,port,address):
    try:
        client = ModbusTcpClient(ip_address, port=port,timeout=3)  # Specify the port.
        connection = client.connect()
        if(connection == False):
            return -222
        request = client.read_holding_registers(int(address), count=1)
        #print(request.registers)
        raw = request.registers[0] if request.registers else -1
        client.close()
        return raw
    except Exception as e:
        print("Get Value Analog Input Erorr: ",e)
        return -1
def connect_db():
    try:
        conn = psycopg2.connect(host="localhost", user="postgres", password="root", database="egateway")
        return conn
    except Exception as e:
        print("Connection Error: ",e)
        return None
        
def get_sensors():
    try:
        conn = connect_db()
        cursor = conn.cursor(cursor_factory=psycopg2.extras.RealDictCursor)
        # sql = "SELECT sensors.id,sensors.stack_id,sensors.name,extra_parameter,formula,analyzer_ip,port,stacks.oxygen_reference FROM sensors LEFT JOIN stacks ON sensors.stack_id = stacks.id where sensors.deleted_at is null ORDER BY id"
        sql = "SELECT id, parameter_id,is_has_reference, ain, formula, ip_analyzer as analyzer_ip, name FROM parameters where formula != '' and stack_id = 5"
        cursor.execute(sql)
        result = cursor.fetchall()
        return result
    except Exception as e:
        print("Connection Error: ",e)
        return None
def update_value(parameterId, measured,raw,isInsertLog = True):
    try:
        global now
        conn = connect_db()
        cursor = conn.cursor()
        # Insert or Update if row exists in sensor_value_logs
        sql = "SELECT id FROM value_logs WHERE parameter_id = {}".format(parameterId)
        cursor.execute(sql)
        result = cursor.fetchall()
        if len(result) > 0:
            sql = "UPDATE value_logs SET measured = {}, corrective = {}, xtimestamp = now() WHERE parameter_id = {}".format(measured,measured,parameterId)
            cursor.execute(sql)
            conn.commit()
            cursor.close()
        else:
            sql = "INSERT INTO value_logs(parameter_id,measured,corrective,xtimestamp) VALUES ({},{},{},now())".format(parameterId,measured,measured)
            cursor.execute(sql)
            conn.commit()
            cursor.close()

        cursor = conn.cursor()
        if(isInsertLog):
            # Insert History Data
            sql = "INSERT INTO measurement_logs(parameter_id,value,voltage,is_averaged,is_das_log,xtimestamp) VALUES ({},{},{},0,1,'{}')".format(parameterId,measured,raw,now)
            cursor.execute(sql)
            conn.commit()
            cursor.close()
        return True
    except Exception as e:
        print("Update Value Error: ",e)
        return None
def update_value_corrective(sensorId,measured,corrected,raw):
    try:
        global now
        conn = connect_db()
        cursor = conn.cursor()
        # Insert or Update if row exists in sensor_value_rca_logs
        sql = "SELECT id FROM sensor_value_rca_logs WHERE sensor_id = {}".format(sensorId)
        cursor.execute(sql)
        result = cursor.fetchall()
        if len(result) > 0:
            sql = "UPDATE sensor_value_rca_logs SET measured = {}, corrected = {}, raw={}, updated_at = now() WHERE sensor_id = {}".format(measured,corrected,raw,sensorId)
            cursor.execute(sql)
            conn.commit()
            cursor.close()
        else:
            sql = "INSERT INTO sensor_value_rca_logs(sensor_id,measured,raw,corrected,created_at) VALUES ({},{},{},{},now())".format(sensorId,measured,raw,corrected)
            cursor.execute(sql)
            conn.commit()
            cursor.close()

        cursor = conn.cursor()
        # Insert History Data
        sql = "INSERT INTO sensor_value_rca(sensor_id,measured,corrected,raw,created_at) VALUES ({},{},{},{},'{}')".format(sensorId,measured,corrected,raw,now)
        cursor.execute(sql)
        conn.commit()
        cursor.close()
        return True
    except Exception as e:
        print("Update Value Error: ",e)
        return None
    
def get_value_o2(stack_id):
    try:
        conn = connect_db()
        cursor = conn.cursor(cursor_factory=psycopg2.extras.RealDictCursor)
        sql = "SELECT measured FROM sensor_value_logs WHERE sensor_id in (select id from sensors where extra_parameter=1 and is_show=1 and stack_id = {} and deleted_at is null)".format(stack_id)
        cursor.execute(sql)
        result = cursor.fetchone()
        if result:
            return result["measured"]
        return None
    except Exception as e:
        print("Get Value O2 Error : ",e)
        return None

def get_config():
    try:
        conn = connect_db()
        cursor = conn.cursor(cursor_factory=psycopg2.extras.RealDictCursor)
        sql = "SELECT * FROM configurations WHERE id=1"
        cursor.execute(sql)
        result = cursor.fetchone()
        return result
    except Exception as e:
        print("get_config(): ",e)
        return None
    
def isEmptyFromPCDAS():
    try:
        conn = connect_db()
        cursor = conn.cursor(cursor_factory=psycopg2.extras.RealDictCursor)
        sql = "SELECT * FROM measurement_logs WHERE is_das_log = 0 ORDER BY xtimestamp DESC LIMIT 1"
        cursor.execute(sql)
        result = cursor.fetchone()
        if(result == None):
            return True
        threeMinAgo = (datetime.now() - timedelta(minutes=3)).strftime("%Y-%m-%d %H:%M:%S")
        if(threeMinAgo >= result['xtimestamp'].strftime("%Y-%m-%d %H:%M:%S")):
            return True
        return False
    except Exception as e:
        print("isEmptyFromPCDAS(): ",e)
        return False
def get_references(parameter_id):
    try:
        conn = connect_db()
        cursor = conn.cursor(cursor_factory=psycopg2.extras.RealDictCursor)
        sql = "SELECT * FROM \"references\" WHERE parameter_id={}".format(parameter_id)
        cursor.execute(sql)
        result = cursor.fetchall()
        return result
    except Exception as e:
        print("Query Get References Error: ",e)
        return None
def main():
    global now
    now =  datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    if(isEmptyFromPCDAS() == False):
        print("PC DAS Data Already Inserted")
        return
    parameters = get_sensors()
    for parameter in parameters:
        try:
            analyzer_ip = parameter["analyzer_ip"] # IP Address Modbus | `ip_analyzer` on parameters table as analyzer_ip
            formula = parameter["formula"] # Formula
            # address_list = parameter["port"].split("|") # Array of Port & AIN
            port = 502 # Port Modbus
            address = parameter["ain"] # Main Address
            _raw = [] # Initialize Raw Value from Analog Input
            # for index,_address in enumerate(address_list): # Loop AIN
            #     if(index > 0):
            #         _raw.append(get_value(analyzer_ip,port,_address))
            
            # Get Raw Value
            raw = round(2.44144E-4*get_value(analyzer_ip,port,address) + 4,2)
            mA = raw
            data = _raw
            try:
                measured = eval(formula) if formula else raw
                if(parameter["is_has_reference"] == 1):
                    references = get_references(parameter["id"])
                    for reference in references:
                        if(measured >= reference["range_start"] and measured <= reference["range_end"]):
                            try:
                                formula = reference["formula"]
                                measured = eval(formula)
                            except Exception as e:
                                measured = -1
                            break
                    
            except Exception as e:
                print("formula error:",e)
                measured = -1
            # Debug
            print("Parameter: "+str(parameter['name'])) 
            print("Formula: "+str(formula)) 
            print("Raw: "+str(raw)) 
            print("Measured: "+str(measured))
            print("====")
                
            if(measured != -1):
                update_value(parameter["id"],measured,raw)
            else:
                update_value(parameter["id"],measured,raw,False)
            
        except Exception as e:
            update_value(parameter["id"],-2,-2,False)
            print("main():", e)

if __name__ == "__main__":
    main()