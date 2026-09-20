<?php

include "get_access_token.php";



function pushmsg (array $data) {

	$url = "https://fcm.googleapis.com/v1/projects/icuweb-f09cf/messages:send";

	$access_token = get_access_token("icuweb-f09cf-firebase-adminsdk-fbsvc-d6532cb53e.json");
    
    $options = array(
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer " . $access_token,
            "Content-Type: application/json",
        ),
        CURLOPT_POSTFIELDS => json_encode($data),
    );
    $curl = curl_init();
    curl_setopt_array($curl, $options);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;

}


function user2master ( int $idutente, string $testo, mysqli $db ) {

	$Mysql="SELECT nomepg FROM personaggio WHERE user_id=$idutente";
	$Result=mysqli_query($db, $Mysql);
	if ( $res=mysqli_fetch_array($Result) ) {
		$nomepg=$res['nomepg'];
	}

	$data = [
        'message' => [
            "notification"=> [
                "title" => "NOTTURNA",
                "body" => $nomepg." ".$testo,

                // 'sound' => 'default',
				// 'notification_priority' => '2'
            ],
            "android" => [
                "notification" => [
                    "channel_id" => "PushPluginChannel"
                ]
            ],
            //'token' => $token,
            'topic' => 'master' 
        ]
    ];

	pushmsg ($data);
}


function master2master ( string $testo ) {
	$data = [
        'message' => [
            "notification"=> [
                "title" => "NOTTURNA",
                "body" => $testo,

                // 'sound' => 'default',
				// 'notification_priority' => '2'
            ],
            "android" => [
                "notification" => [
                    "channel_id" => "PushPluginChannel"
                ]
            ],
            //'token' => $token,
            'topic' => 'master' 
        ]
    ];

	pushmsg ($data);

}

function master2user ( int $idutente , string $testo , mysqli $db) {

	$Mysql="SELECT token FROM push_subscriptions WHERE user_id=$idutente";
	$Result=mysqli_query($db, $Mysql);
	$res=mysqli_fetch_array($Result);

	if ($res['token'] != "" ) {

		$token= $res['token'];

		$data = [
			'message' => [
				"notification"=> [
					"title" => "NOTTURNA",
					"body" => $testo,
	
					// 'sound' => 'default',
					// 'notification_priority' => '2'
				],
				"android" => [
					"notification" => [
						"channel_id" => "PushPluginChannel"
					]
				],
				'token' => $token,
				//'topic' => 'master' 
			]
		];
	
		pushmsg ($data);

	} else {

		// NON FACCIO NULLA

	}




}




function user2user ( string $nomepg, int $destinatario , string $testo , mysqli $db) {

	$Mysql="SELECT token FROM push_subscriptions WHERE user_id=$destinatario";
	$Result=mysqli_query($db, $Mysql);
	$res=mysqli_fetch_array($Result);

	if ($res['token'] != "" ) {

		$token= $res['token'];

        // echo $token . "<p>" ; 

		$data = [
			'message' => [
				"notification"=> [
					"title" => "NOTTURNA",
					"body" => "TELEPATIA da ". $nomepg . ": " . $testo,
	
					// 'sound' => 'default',
					// 'notification_priority' => '2'
				],
				"android" => [
					"notification" => [
						"channel_id" => "PushPluginChannel"
					]
				],
				'token' => $token,
				//'topic' => 'master' 
			]
		];
	
		pushmsg ($data);

	} else {

		// NON FACCIO NULLA

	}




}


function master2clan ( int $idclan , string $nomeclan, string $clanimg, string $testo , mysqli $db) {

	// idclan non è usato, il topic è il nome del clan

	$data = [
        'message' => [
            "notification"=> [
                "title" => "Ivory Cross University",
                "body" => "Messaggio per clan ".$nomeclan . ". ". $testo,
            ],
            "android" => [
                "notification" => [
                    "channel_id" => "PushPluginChannel",
                    'image' => "https://www.roma-by-night.it/imgs/".$clanimg,

                ]
            ],

            'topic' => $nomeclan 
        ]
    ];
	pushmsg ($data);

	

}





?>
