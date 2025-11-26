from labjack import ljm
from datetime import datetime, timedelta
import psycopg2
import psycopg2.extras

def connect_db():
    try:
        conn = psycopg2.connect(host="localhost", user="postgres", password="root", database="egateway")
        return conn
    except Exception as e:
        print("Connection Error: ",e)
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
def get_sensors():
    try:
        conn = connect_db()
        cursor = conn.cursor(cursor_factory=psycopg2.extras.RealDictCursor)
        # sql = "SELECT sensors.id,sensors.stack_id,sensors.name,extra_parameter,formula,analyzer_ip,port,stacks.oxygen_reference FROM sensors LEFT JOIN stacks ON sensors.stack_id = stacks.id where sensors.deleted_at is null ORDER BY id"
        sql = "SELECT id, parameter_id,is_has_reference, ain, formula, ip_analyzer as analyzer_ip, name FROM parameters where formula != '' and stack_id in (1,2,3,4)"
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
    
def main():
    if(isEmptyFromPCDAS() == False):
        print("PC DAS Data Already Inserted")
        return
    sensors = get_sensors()
    print("Count sensor: ",len(sensors))
    for sensor in sensors:
        ipAddress = sensor['analyzer_ip']
        ain = sensor['ain']
        formula = sensor['formula']
        parameterId = sensor['parameter_id']
        handle = None
        try:
            handle = ljm.openS(ipAddress, "ANY", "ANY")
            raw = ljm.eReadName(ipAddress, ain)
            measured = eval(formula)
            update_value(parameterId, measured, raw)
        except Exception as e:
            print("Error: ",e)
            update_value(parameterId, -222, -222, False)
            continue
        finally:
            if(handle != None):
                ljm.close(handle)
            
if __name__ == "__main__":
    main()