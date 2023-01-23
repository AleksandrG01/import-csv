<?php

$host      = 'localhost';
$db_name   = 'import';
$db_user   = 'root';
$db_passwd = '';
$file      = 'test.csv';
$delimiter = ',';
// $verbose   = true;


//уходим в ошибку если что-то не так
try {

	// PDO() стандартная функция PHP для чтения csv файлов
    $db = new PDO("mysql:host=$host;dbname=$db_name", $db_user, $db_passwd, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    $stmt = $db->prepare("INSERT INTO my1 (`id`, `name_group`, `name`, `price`, `remainder`, `description`) VALUES (?, ?, ?, ?, ?, ?) ");

	//проверяем файл
    if (!file_exists($file)){
        throw new Exception("File $file not found!");
    }
	//открываем файл
    $f = fopen($file, 'r+');

	//устанавливаем переменную
    $data = array();
	//По условию читаем строку из файла и производим разбор данных CSV
    while($data[]=fgetcsv($f, 0, $delimiter)){}

	//Выводим ошибку если (пусто)
    if (empty($data)) {
        throw new Exception("Пустые данные. Проверьте исходный файл.");
    }
    $i=0;
	//перебор данных в массиве и запись в БД
    foreach ($data as $entry) {
        if (!is_array($entry) || empty($entry[0])) {
            continue;
        }
        // if ($verbose){
        //     print "\nProcessing entry ID: ". $entry[0];
        // }
		//запуск подготовленного выше процесса
        $stmt->execute($entry);
        $i++;
    }
    print "\n\n$i строки успешно обработаны";

}
catch (Exception $e){
    print "Ошибка: " . $e->getMessage();
}

