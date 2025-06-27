<?php

	//Ruta del proyecto
	const BASE_URL = "https://botica-532h.onrender.com";
	const TITLE = "SISTEMA FARMACEUTICO";

	// Ruta del dominio del proyecto
    // const BASE_URL = "https://sana-plus.infy.uk/";
    // const TITLE = "SISTEMA FARMACEUTICO";
	//Zona horaria
    date_default_timezone_set ("America/Lima");
    
    //Delimitadores decimal y millar ejem. 24,1998.00
	const SPD = ".";
	const SPM = ",";

	//Simbolo de moneda
	const SMONEY = "S/.";

	//Simbolo de moneda
	const STAX = "IGV";

	//Datos para encryptacion
	const METHODC="AES-256-CBC";
    const SECRET_KEYC='$#*ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890#*';
	const SECRET_IVC='20001109108103975194753';
//LISTO...
