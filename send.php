<?php

function debug_to_console($data) {
  $output = $data;
  if (is_array($output))
      $output = implode(',', $output);

  echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}
function post_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

$phone = $email = "";
$phone = post_input($_POST["phone"]);
$email = post_input($_POST["email"]);
// debug_to_console($phone);
// debug_to_console($email);
$subdomain = '1982dmitrii19821982'; //Поддомен нужного аккаунта
$link = 'https://' . $subdomain . '.amocrm.ru/oauth2/access_token'; //Формируем URL для запроса
/** Соберем данные для запроса */
$data = [
	'client_id' => '59db0c33-7866-42ee-8312-6b2d8ebbe0f7', // id нашей интеграции
	'client_secret' => 'UBTaIDuw8hVSviCIt7ZOZrWytjNFzSwnmMnc8OYu4JADQatcW7cnqxWYSqxzxMoA', // секретный ключ нашей интеграции
	'grant_type' => 'authorization_code',
	'code' => 'def502004f5e4ef7060db370846e88a0c89fe3241a2fb4e364bcfb82b3f4ecd50f77521d1a9235d7d0c6a2f735e553aa3ea59f0ab97c3650ce98148bd9aec99da71b13424213ba3502fe545bb2bd798db1d16efdbb83e96a6c5745ada530d10c877d18a39c14a414eb11c94a174599f547475dbce16d38c23c7cb0be064bf6260e253522eefe393a98d825a6a015f16e93c9c97ed6e09d4f8eb78a87a18cd020d3da13f44387564bcce47d1000fdacaf66d8d67716b574ca7989ac81464097bd5e8d748ee719023bd52fd6d0e2b8283bbea599269a9dc48e1f40fdd884ae13bc2a53ca73cb2cc40d0e721adbce0f78b49b92cb3d18344b920f3e2904dc58154b94e51759e9ec3c2ca0c3762bd954cbad6e90fe4bb7fdad24238e0c74fd23879a61dcbb2d481e492343bf3bddf659b0e0fb44e7153d0110408093b28e01c1ff34d411bc8bcfd534afd42e66abd1e02b8f50c4d79465283661d1aae96255851e9bc0e0e9f98b55b2e2560545d1efd0206bec07cbfa367f3fd14326f785458293d382e5dbcd530c0425f88e026730c238900c7b159ab6a898560b900d443144d6f1344a66e63adcf048cbbe9a6a18465c14fd56e204575aaa1d78ebcfe4f0', // код авторизации нашей интеграции
	'redirect_uri' => 'https://example.com',// домен сайта нашей интеграции
];

$curl = curl_init(); //Сохраняем дескриптор сеанса cURL
/** Устанавливаем необходимые опции для сеанса cURL  */
curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
curl_setopt($curl,CURLOPT_URL, $link);
curl_setopt($curl,CURLOPT_HTTPHEADER,['Content-Type:application/json']);
curl_setopt($curl,CURLOPT_HEADER, false);
curl_setopt($curl,CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);
$out = curl_exec($curl); //Инициируем запрос к API и сохраняем ответ в переменную
$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);
/** Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
$code = (int)$code;

// коды возможных ошибок
$errors = [
	400 => 'Bad request',
	401 => 'Unauthorized',
	403 => 'Forbidden',
	404 => 'Not found',
	500 => 'Internal server error',
	502 => 'Bad gateway',
	503 => 'Service unavailable',
];

try
{
	/** Если код ответа не успешный - возвращаем сообщение об ошибке  */
	if ($code < 200 || $code > 204) {
		throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undefined error', $code);
       
	}
}
catch(\Exception $e)
{
	die('Ошибка: ' . $e->getMessage() . PHP_EOL . 'Код ошибки: ' . $e->getCode());
}

/**
 * Данные получаем в формате JSON, поэтому, для получения читаемых данных,
 * нам придётся перевести ответ в формат, понятный PHP
 */

$response = json_decode($out, true);

$access_token = $response['access_token']; //Access токен
$refresh_token = $response['refresh_token']; //Refresh токен
$token_type = $response['token_type']; //Тип токена
$expires_in = $response['expires_in']; //Через сколько действие токена истекает

// выведем наши токены. Скопируйте их для дальнейшего использования
// access_token будет использоваться для каждого запроса как идентификатор интеграции
// var_dump($access_token);
// debug_to_console($access_token);

// var_dump($refresh_token);
// debug_to_console($refresh_token);

$arrContactParams = [
	// поля для сделки 
	"PRODUCT" => [
		"nameForm"	=> "Заявка Савенков",

		// "nameProduct" 	=> "Название товара",
		// "price"		=> "Цена",
		// "descProduct"	=> "Описание заказа",

		"namePerson"	=> "Контакт Савенков",
		"phonePerson"	=> $phone,
		"emailPerson"	=> $email,
		//"messagePerson"	=> "Сообщение от пользователя",
	],
	// поля для контакта 
	"CONTACT" => [
		"namePerson"	=> "Контакт Савенков",
		"phonePerson"	=> $phone,
		"emailPerson"	=> $email,
		// "messagePerson"	=> "Сообщение от пользователя",
	]
];
amoAddContact($access_token, $arrContactParams);
function amoAddContact($access_token, $arrContactParams) {

  $contacts['request']['contacts']['add'] = array(
  [
	'name' => $arrContactParams["CONTACT"]["namePerson"],
	'tags' => 'авто отправка',
	'custom_fields'	=> [
		// ИМЯ ПОЛЬЗОВАТЕЛЯ 
		[
			'id'	=> 518661,
			"values" => [
				[
					"value" => $arrContactParams["CONTACT"]["namePerson"],
				]
			]
		],
		// ТЕЛЕФОН
		[
			'id'	=> 1426544,
      "field_name" => "Телефон",
			"values" => [
				[
					"value" => $arrContactParams["CONTACT"]["phonePerson"],
				]
					]
		],
		// EMAIL 
		[
			'id'	=> 518595,
			"values" => [
				[
					"value" => $arrContactParams["CONTACT"]["emailPerson"],
				]
			]
		]
    // ,
		// // СООБЩЕНИЕ
		// [
		// 	'id'	=> 532695,
		// 	"values" => [
		// 		[
		// 			"value" => $arrContactParams["CONTACT"]["messagePerson"],
		// 		]
		// 	]
		// ]
	]
]
);


	/* Формируем заголовки */
	$headers = [
		"Accept: application/json",
		'Authorization: Bearer ' . $access_token
	];
	
	$link='https://1982dmitrii19821982.amocrm.ru/private/api/v2/json/contacts/set';

	$curl = curl_init(); //Сохраняем дескриптор сеанса cURL
	/** Устанавливаем необходимые опции для сеанса cURL  */
	curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
	curl_setopt($curl,CURLOPT_URL, $link);
	curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
	curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($contacts));
	curl_setopt($curl,CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl,CURLOPT_HEADER, false);
	curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
	curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);
	$out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
	$code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
	curl_close($curl);
	$Response=json_decode($out,true);
	$account=$Response['response']['account'];
	echo '<b>Данные о пользователе:</b>'; echo '<pre>'; print_r($Response); echo '</pre>';

	return $Response["response"]["contacts"]["add"]["0"]["id"];

}
//amoAddTask($access_token, $arrContactParams, false);
function amoAddTask($access_token, $arrContactParams, $contactId = false) {


  $arrTaskParams = [  
  'add' => [
    0 => [
      'name'  => $arrContactParams["PRODUCT"]["nameForm"],
        //'price'         => $arrContactParams["PRODUCT"]["price"],
        'pipeline_id'   => '9168',
        'tags'          => [
          'авто отправка',
          $arrContactParams["PRODUCT"]["nameForm"]
        ],
        'status_id'     => '10937736',
        'custom_fields'	=> [
          /* ОПИСАНИЕ ЗАКАЗА */
          // [
          //   'id'	=> 531865,
          //   "values" => [
          //     [
          //       "value" => $arrContactParams["PRODUCT"]["descProduct"],
          //     ]
          //   ]
          // ],
          /* ИМЯ ПОЛЬЗОВАТЕЛЯ */
          [
            'id'	=> 525741,
            "values" => [
              [
                "value" => $arrContactParams["PRODUCT"]["namePerson"],
              ]
            ]
          ],
          /* ТЕЛЕФОН */
          [
            'id'	=> 525687,
            "values" => [
              [
                "value" => $arrContactParams["PRODUCT"]["phonePerson"],
              ]
            ]
          ],
          /* EMAIL */
          [
            'id'	=> 525739,
            "values" => [
              [
                "value" => $arrContactParams["PRODUCT"]["emailPerson"],
              ]
            ]
          ],
          /* СООБЩЕНИЕ */
          // [
          //   'id'	=> 528257,
          //   "values" => [
          //     [
          //       "value" => $arrContactParams["PRODUCT"]["messagePerson"],
          //     ]
          //   ]
          // ],
        ],
  
        'contacts_id' => [
          0 => $contactId,
        ],
      ],
    ],
  ];
  
  
    $link = "https://1982dmitrii19821982.amocrm.ru/api/v2/leads";
  
    $headers = [
          "Accept: application/json",
          'Authorization: Bearer ' . $access_token
    ];
  
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl, CURLOPT_USERAGENT, "amoCRM-API-client-
    undefined/2.0");
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($arrTaskParams));
    curl_setopt($curl, CURLOPT_URL, $link);
    curl_setopt($curl, CURLOPT_HEADER,false);
    curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__)."/cookie.txt");
    curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__)."/cookie.txt");
    $out = curl_exec($curl);
    curl_close($curl);
    $result = json_decode($out,TRUE);
  
  }
  /* в эту функцию мы передаём текущий refresh_token */
function returnNewToken($token) {

	$link = 'https://1982dmitrii19821982.amocrm.ru/oauth2/access_token';

	/** Соберем данные для запроса */
	$data = [
    'client_id' => '59db0c33-7866-42ee-8312-6b2d8ebbe0f7',
    'client_secret' => 'UBTaIDuw8hVSviCIt7ZOZrWytjNFzSwnmMnc8OYu4JADQatcW7cnqxWYSqxzxMoA',
		'grant_type' => 'refresh_token',
		'refresh_token' => $token,
		'redirect_uri' => 'https://example.com',
	];

	/**
	 * Нам необходимо инициировать запрос к серверу.
	 * Воспользуемся библиотекой cURL (поставляется в составе PHP).
	 * Вы также можете использовать и кроссплатформенную программу cURL, если вы не программируете на PHP.
	 */
	$curl = curl_init(); //Сохраняем дескриптор сеанса cURL
	/** Устанавливаем необходимые опции для сеанса cURL  */
	curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
	curl_setopt($curl,CURLOPT_URL, $link);
	curl_setopt($curl,CURLOPT_HTTPHEADER,['Content-Type:application/json']);
	curl_setopt($curl,CURLOPT_HEADER, false);
	curl_setopt($curl,CURLOPT_CUSTOMREQUEST, 'POST');
	curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($data));
	curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
	curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);
	$out = curl_exec($curl); //Инициируем запрос к API и сохраняем ответ в переменную
	$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	curl_close($curl);
	/** Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
	$code = (int)$code;
	$errors = [
		400 => 'Bad request',
		401 => 'Unauthorized',
		403 => 'Forbidden',
		404 => 'Not found',
		500 => 'Internal server error',
		502 => 'Bad gateway',
		503 => 'Service unavailable',
	];

	try
	{
		/** Если код ответа не успешный - возвращаем сообщение об ошибке  */
		if ($code < 200 || $code > 204) {
			throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undefined error', $code);
		}
	}
	catch(\Exception $e)
	{
		die('Ошибка: ' . $e->getMessage() . PHP_EOL . 'Код ошибки: ' . $e->getCode());
	}

	/**
	 * Данные получаем в формате JSON, поэтому, для получения читаемых данных,
	 * нам придётся перевести ответ в формат, понятный PHP
	 */

	$response = json_decode($out, true);

	if($response) {

		/* записываем конечное время жизни токена */
		$response["endTokenTime"] = time() + $response["expires_in"];

		$responseJSON = json_encode($response);

		/* передаём значения наших токенов в файл */
		$filename = "token.json";
		$f = fopen($filename,'w');
		fwrite($f, $responseJSON);
		fclose($f);

		$response = json_decode($responseJSON, true);

		return $response;
	}
	else {
		return false;
	}

}
//returnNewToken($refresh_token);
function amoCRMScript($paramsTask) {

	/* получаем значения токенов из файла */
	$dataToken = file_get_contents("token.json");
	$dataToken = json_decode($dataToken, true);

	/* проверяем, истёкло ли время действия токена Access */
	if($dataToken["endTokenTime"] < time()) {
		/* запрашиваем новый токен */
		$dataToken = returnNewToken($dataToken["refresh_token"]);
		$newAccess_token = $dataToken["access_token"];
	}
	else {
		$newAccess_token = $dataToken["access_token"];
	}

	if($paramsTask["CONTACT"]) {
		$idContact = amoAddContact($newAccess_token, $paramsTask);
	}

	amoAddTask($newAccess_token, $paramsTask, $idContact);

}
amoCRMScript($arrContactParams);