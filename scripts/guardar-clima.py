#!/usr/bin/env python
#script para guardar predicciones de clima bajados desde la weather channel

import urllib2
import json
import MySQLdb
import time

key_ID = 'a0c04db7ae160e62'
coord_aeropuerto = '-26.84086037,-65.10494232'

# pedir predicciones a 10 dias a nivel horario
def prediccion10DiasHorario(key_ID, coord):
	try: f = urllib2.urlopen('http://api.wunderground.com/api/'+key_ID+'/hourly10day/q/'+coord+'.json')
	except: return ''
	json_string = f.read()
	parsed_json = json.loads(json_string)
	f.close()
	return parsed_json

# conectar a una base de datos MySQL
def conectarMySQL():
	db = MySQLdb.connect(host="localhost", user="root", passwd="123456", db="clima_wu")
	return db

# insertar una prediccion
def insertarForecast(db, est, obs, idCapture):
	cur = db.cursor()

	date = "'%s-%s-%s %s:%s:00'" %(obs["FCTTIME"]["year"], obs["FCTTIME"]["mon_padded"], obs["FCTTIME"]["mday_padded"], obs["FCTTIME"]["hour_padded"], obs["FCTTIME"]["min"])
	temp = obs['temp']['metric'] if len(obs['temp']['metric'])>0  else '-999'
	wspd = obs['wspd']['metric'] if len(obs['wspd']['metric'])>0  else '-999'
	wdir = obs['wdir']['degrees'] if len(obs['wdir']['degrees'])>0  else '-999'
	humidity = obs['humidity'] if len(obs['humidity'])>0  else '-999'
	windchill = obs['windchill']['metric'] if len(obs['windchill']['metric'])>0  else '-999'
	heatindex = obs['heatindex']['metric'] if len(obs['heatindex']['metric'])>0  else '-999'
	pop = obs['pop'] if len(obs['pop'])>0  else '-999'
	mslp = obs['mslp']['metric'] if len(obs['mslp']['metric'])>0  else '-999'

	try: temp = "'%s'"%temp if float(temp)>-999  else 'NULL'
	except: temp = 'NULL'
	try: wspd = "'%s'"%wspd if float(wspd)>-999  else 'NULL'
	except: wspd = 'NULL'
	try: wdir = "'%s'"%wdir if float(wdir)>-999  else 'NULL'
	except: wdir = 'NULL'
	try: humidity = "'%s'"%humidity if float(humidity)>-999  else 'NULL'
	except: humidity = 'NULL'
	try: windchill = "'%s'"%windchill if float(windchill)>-999  else 'NULL'
	except: windchill = 'NULL'
	try: heatindex = "'%s'"%heatindex if float(heatindex)>-999  else 'NULL'
	except: heatindex = 'NULL'
	try: pop = "'%s'"%pop if float(pop)>-999  else 'NULL'
	except: pop = 'NULL'
	try: mslp = "'%s'"%mslp if float(mslp)>-999  else 'NULL'
	except: mslp = 'NULL'

	fields = "idCapture, idStation, dates, temp, wspd, wdir, humidity, windchill, heatindex, pop, mslp"
	query = "INSERT INTO clima_wu.forecasts (%s) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)" % (
		fields, str(idCapture), str(est), date, temp, wspd, wdir, humidity, windchill, heatindex, pop, mslp)
	
	res = cur.execute(query)
	db.commit()
	cur.close()
	return res

# insertar una captura para predicciones
def insertarCaptura(db, est):
	cur = db.cursor()
	date = time.strftime("'%Y-%m-%d %H:%M:%S'")
	fields = "idStation, dates"
	query = "INSERT INTO clima_wu.captures (%s) VALUES (%s,%s)" % (
		fields, str(est), date)
	res = 0
	r = cur.execute(query)
	if r == 1:
		db.commit()
		r = cur.execute("SELECT MAX(idCapture) FROM clima_wu.captures")
		if r == 1:
			for row in cur.fetchall():
			    res = row[0]
	cur.close()
	return res


def procesarPredicciones():
	est = 1
	db = conectarMySQL()

	idCapture = insertarCaptura(db, est)
	if idCapture == 0:
		return False

	intentos = 100
	while intentos > 0:
		# pedir datos del dia
		parsed_json = prediccion10DiasHorario(key_ID, coord_aeropuerto)
		time.sleep( 6.5 ) # LIMITACION POR USO GRATUITO

		# si todo va bien
		if len(parsed_json) > 0:
			intentos = 0
			# por cada muestra
			for obs in parsed_json['hourly_forecast']:
				# insertarla en la base de datos
				res = insertarForecast(db, est, obs, idCapture)
				if res != 1:
					print "Hubo un error al prediccion.", obs["FCTTIME"]
					return False
		else:
			# quito un intento y espero para el proximo
			intentos = intentos - 1

		print intentos

	db.close()
	return True	

procesarPredicciones()


