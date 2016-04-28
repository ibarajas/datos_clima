import urllib2
import json
import MySQLdb
import time

key_ID = 'a0c04db7ae160e62'
coord_aeropuerto = '-26.84086037,-65.10494232'

# me dice si un anio es bisiesto
def esBisiesto(a):
	return ((a%4)==0) & ((a%100)!=0) | ((a%400)==0)

# me dice si el vector contiene una fecha valida. El vector se compone de anio, mes, dia, hora y minutos
def fechaValida(f):
	dias_mes = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31]
	if f[1]>12:
		return False
	elif ((f[1]!=2) | (esBisiesto(f[0])==False)):
		return f[2]<=dias_mes[f[1]-1]
	else:
		return f[2]<=dias_mes[1]+1

# obtener informacion geografica dada una coordenada
def geolooukp(key_ID, coord):
	f = urllib2.urlopen('http://api.wunderground.com/api/'+key_ID+'/geolookup/q/'+coord+'.json')
	json_string = f.read()
	parsed_json = json.loads(json_string)
	f.close()
	return parsed_json

# obtener informacion historica de un determinado dia. Por lo general da valores horarios.
def history(key_ID, coord, YYYYMMDD):
	f = urllib2.urlopen('http://api.wunderground.com/api/'+key_ID+'/history_'+YYYYMMDD+'/q/'+coord+'.json')
	json_string = f.read()
	parsed_json = json.loads(json_string)
	f.close()
	return parsed_json

# pedir predicciones a 10 dias a nivel horario
def prediccion10DiasHorario(key_ID, coord):
	f = urllib2.urlopen('http://api.wunderground.com/api/'+key_ID+'/hourly10day/q/'+coord+'.json')
	json_string = f.read()
	parsed_json = json.loads(json_string)
	f.close()
	return parsed_json

# conectar a una base de datos MySQL
def conectarMySQL():
	db = MySQLdb.connect(host="localhost", user="root", passwd="123456", db="clima_wu")
	return db

# insertar una observacion (muestra) de clima para una estacion
def insertarSample(db, est, obs):
	cur = db.cursor()

	date = "'%s-%s-%s %s:%s:00'" %(obs['date']['year'], obs['date']['mon'], obs['date']['mday'], obs['date']['hour'], obs['date']['min'])
	tempm = obs['tempm'] if len(obs['tempm'])>0  else '-999'
	windchillm = obs['windchillm'] if len(obs['windchillm'])>0 else '-999'
	heatindexm = obs['heatindexm'] if len(obs['heatindexm'])>0 else '-999'
	hum = obs['hum'] if len(obs['hum'])>0 else '-999'
	wspdm = obs['wspdm'] if len(obs['wspdm'])>0 else '-999'
	wgustm = obs['wgustm'] if len(obs['wgustm'])>0 else '-999'
	wdird = obs['wdird'] if len(obs['wdird'])>0 else '-999'
	pressurem = obs['pressurem'] if len(obs['pressurem'])>0 else '-999'
	precipm = obs['precipm'] if len(obs['precipm'])>0 else '-999'

	try: tempm = "'%s'"%tempm if float(tempm)>-999  else 'NULL'
	except: tempm = 'NULL'
	try: windchillm = "'%s'"%windchillm if float(windchillm)>-999 else 'NULL'
	except: windchillm = 'NULL'
	try: heatindexm = "'%s'"%heatindexm if float(heatindexm)>-999 else 'NULL'
	except: heatindexm = 'NULL'
	try: hum = "'%s'"%hum if float(hum)>-999 else 'NULL'
	except: hum = 'NULL'
	try: wspdm = "'%s'"%wspdm if float(wspdm)>-999 else 'NULL'
	except: wspdm = 'NULL'
	try: wgustm = "'%s'"%wgustm if float(wgustm)>-999 else 'NULL'
	except: wgustm = 'NULL'
	try: wdird = "'%s'"%wdird if float(wdird)>-999 else 'NULL'
	except: wdird = 'NULL'
	try: pressurem = "'%s'"%pressurem if float(pressurem)>-999 else 'NULL'
	except: pressurem = 'NULL'
	try: precipm = "'%s'"%precipm if float(precipm)>-999 else 'NULL'
	except: precipm = 'NULL'

	fields = "idStation, dates, tempm, windchillm, heatindexm, hum, wspdm, wgustm, wdird, pressurem, precipm"
	query = "INSERT INTO clima_wu.samples (%s) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)" % (
		fields, str(est), date, tempm, windchillm, heatindexm, hum, wspdm, wgustm, wdird, pressurem, precipm)
	
	res = cur.execute(query)
	db.commit()
	cur.close()
	return res

# insertar una observacion promedio (muestra diaria) de clima para una estacion
def insertarDailySummary(db, est, obs):
	cur = db.cursor()

	date = "'%s-%s-%s %s:%s:00'" %(obs['date']['year'], obs['date']['mon'], obs['date']['mday'], obs['date']['hour'], obs['date']['min'])
	mintempm = obs['mintempm'] if len(obs['mintempm'])>0  else '-999'
	maxtempm = obs['maxtempm'] if len(obs['maxtempm'])>0 else '-999'
	meantempm = obs['meantempm'] if len(obs['meantempm'])>0 else '-999'
	minhumidity = obs['minhumidity'] if len(obs['minhumidity'])>0 else '-999'
	maxhumidity = obs['maxhumidity'] if len(obs['maxhumidity'])>0 else '-999'
	humidity = obs['humidity'] if len(obs['humidity'])>0 else '-999'
	minpressurem = obs['minpressurem'] if len(obs['minpressurem'])>0 else '-999'
	maxpressurem = obs['maxpressurem'] if len(obs['maxpressurem'])>0 else '-999'
	meanpressurem = obs['meanpressurem'] if len(obs['meanpressurem'])>0 else '-999'
	minwspdm = obs['minwspdm'] if len(obs['minwspdm'])>0 else '-999'
	maxwspdm = obs['maxwspdm'] if len(obs['maxwspdm'])>0 else '-999'
	meanwindspdm = obs['meanwindspdm'] if len(obs['meanwindspdm'])>0 else '-999'
	meanwdird = obs['meanwdird'] if len(obs['meanwdird'])>0 else '-999'
	precipm = obs['precipm'] if len(obs['precipm'])>0 else '-999'
	heatingdegreedays = obs['heatingdegreedays'] if len(obs['heatingdegreedays'])>0 else '-999'

	try: mintempm = "'%s'"%mintempm if float(mintempm)>-999  else 'NULL'
	except: mintempm = 'NULL'
	try: maxtempm = "'%s'"%maxtempm if float(maxtempm)>-999  else 'NULL'
	except: maxtempm = 'NULL'
	try: meantempm = "'%s'"%meantempm if float(meantempm)>-999  else 'NULL'
	except: meantempm = 'NULL'
	try: minhumidity = "'%s'"%minhumidity if float(minhumidity)>-999  else 'NULL'
	except: minhumidity = 'NULL'
	try: maxhumidity = "'%s'"%maxhumidity if float(maxhumidity)>-999  else 'NULL'
	except: maxhumidity = 'NULL'
	try: humidity = "'%s'"%humidity if float(humidity)>-999  else 'NULL'
	except: humidity = 'NULL'
	try: minpressurem = "'%s'"%minpressurem if float(minpressurem)>-999  else 'NULL'
	except: minpressurem = 'NULL'
	try: maxpressurem = "'%s'"%maxpressurem if float(maxpressurem)>-999  else 'NULL'
	except: maxpressurem = 'NULL'
	try: meanpressurem = "'%s'"%meanpressurem if float(meanpressurem)>-999  else 'NULL'
	except: meanpressurem = 'NULL'
	try: minwspdm = "'%s'"%minwspdm if float(minwspdm)>-999  else 'NULL'
	except: minwspdm = 'NULL'
	try: maxwspdm = "'%s'"%maxwspdm if float(maxwspdm)>-999  else 'NULL'
	except: maxwspdm = 'NULL'
	try: meanwindspdm = "'%s'"%meanwindspdm if float(meanwindspdm)>-999  else 'NULL'
	except: meanwindspdm = 'NULL'
	try: meanwdird = "'%s'"%meanwdird if float(meanwdird)>-999  else 'NULL'
	except: meanwdird = 'NULL'
	try: precipm = "'%s'"%precipm if float(precipm)>-999  else 'NULL'
	except: precipm = 'NULL'
	try: heatingdegreedays = "'%s'"%heatingdegreedays if float(heatingdegreedays)>-999  else 'NULL'
	except: heatingdegreedays = 'NULL'

	fields = "idStation, dates, mintempm, maxtempm, meantempm, minhumidity, maxhumidity, humidity, minpressurem, maxpressurem, meanpressurem, minwspdm, maxwspdm, meanwindspdm, meanwdird, precipm, heatingdegreedays"
	query = "INSERT INTO clima_wu.dailysummary (%s) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)" % (
		fields, str(est), date, mintempm, maxtempm, meantempm, minhumidity, maxhumidity, humidity, 
		minpressurem, maxpressurem, meanpressurem, minwspdm, maxwspdm, meanwindspdm, meanwdird, precipm, heatingdegreedays)
	res = cur.execute(query)
	db.commit()
	cur.close()
	return res


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


def procesarHistorico(est, fecha):
	anio_limite = fecha[0]+1
	db = conectarMySQL()

	# busco un anio por vez
	while (fecha[0] != anio_limite):
		print fecha;

		# pedir datos del dia
		YYYYMMDD = "%04d%02d%02d" % (fecha[0], fecha[1], fecha[2])
		parsed_json = history(key_ID, coord_aeropuerto, YYYYMMDD)
		time.sleep( 10 ) # LIMITACION POR USO GRATUITO

		# guardo promedios diarios
		dailysummary = parsed_json['history']['dailysummary']
		res = insertarDailySummary(db, est, dailysummary[0])
		if res != 1:
			print "Hubo un error al guardar los promedios diarios.", fecha
			break

		# por cada muestra
		for obs in parsed_json['history']['observations']:
			# insertarla en la base de datos
			res = insertarSample(db, est, obs)
			if res != 1:
				print "Hubo un error al guardar muestra diaria.", fecha
				break

		# paso al siguiente dia
		fecha[2] = fecha[2] + 1
		if fechaValida(fecha) == False:
			fecha[2] = 1
			fecha[1] = fecha[1] + 1
			if fechaValida(fecha) == False:
				fecha[1] = 1
				fecha[0] = fecha[0] + 1

	db.close()

procesarHistorico(1, [2014,11,16])


