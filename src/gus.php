<?php

require_once '../vendor/autoload.php';

use Dotenv\Dotenv;
use GusApi\GusApi;
use GusApi\RegonConstantsInterface;
use GusApi\Exception\InvalidUserKeyException;
use GusApi\ReportTypes;
use GusApi\ReportTypeMapper;


$dotenv = Dotenv::createImmutable(__DIR__ . '/../'); 
$dotenv->load();

header('Content-Type: application/json'); 

$apiKey = $_ENV['GUS_API_KEY'] ?? null;

$gus = new GusApi(
    $apiKey, 
    new \GusApi\Adapter\Soap\SoapAdapter(
        RegonConstantsInterface::BASE_WSDL_URL,
        RegonConstantsInterface::BASE_WSDL_ADDRESS 
    )
);

$mapper = new ReportTypeMapper();

$response = [
    'success' => false,
    'message' => 'Nie znaleziono danych'
];



try {
    $nipToCheck = $_POST['nip'] ?? '';
    
    if (empty($nipToCheck) || strlen($nipToCheck) !== 10) {
        $response['message'] = "Wprowadzony NIP jest nieprawidłowy: ";
        echo json_encode($response);
        exit();
    }
    
    $sessionId = $gus->login();

    $gusReports = $gus->getByNip($sessionId, $nipToCheck);

    if (empty($gusReports)) {
        $response['message'] = "Nie znaleziono danych";
        echo json_encode($response);
        exit();
    }

    $gusReports = $gusReports[0];
    $reportType = $mapper->getReportType($gusReports);

    $data = $gus->getFullReport($sessionId, $gusReports, $reportType);
    $dane = $data->dane;

    $response = [
        'success' => true,
        'nip' => (string) $dane->praw_nip ?? '', 
        'adres' => [
            'ulica' => (string) $dane->praw_adSiedzUlica_Nazwa ?? '',
            'numer_nieruchomosci' => (string) $dane->praw_adSiedzNumerNieruchomosci ?? '',
            'numer_lokalu' => (string) $dane->praw_adSiedzNumerLokalu ?? '',
            'kod_pocztowy' => (string) $dane->praw_adSiedzKodPocztowy ?? '',
            'miejscowosc' => (string) $dane->praw_adSiedzMiejscowosc_Nazwa ?? '',
        ],
    ];
    

    
} catch (InvalidUserKeyException $e) {
    echo 'Bad user key';
} catch (\GusApi\Exception\NotFoundException $e) {
    echo 'No data found <br>';
    echo 'For more information read server message below: <br>';
    echo $gus->getResultSearchMessage($sessionId);
}

echo json_encode($response);

