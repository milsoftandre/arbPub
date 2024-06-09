<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('/makevideo', function (Request $request) {
    $apiA = (new \App\Models\Apiip());
    $req = 'Напиши случайную и популярную цитату известного человека';
    $response = $apiA->ChatGpt($req, 'В формате : Цитата||Имя фамилия');
    $finalRez = str_replace('"',"",$response->choices[0]->message->content);

    $MakeVideo = new \App\Models\MakeVideo(explode("||",$finalRez)[0],explode("||",$finalRez)[1],'');

    $video = $MakeVideo->Make();
    $telegramBotToken = env('TELEGRAM_BOT_TOKEN');
    $inputFile = \Telegram\Bot\FileUpload\InputFile::create($video);
    $telegram = new \Telegram\Bot\Api($telegramBotToken);
        $chat_id = \App\Models\Channel::find(6)->channel;
    $response = $telegram->sendVideo([
        'chat_id' => $chat_id,
        'video' => $inputFile,
        'width' => 300,                      // int              - (Optional). Video width
        'height' => 600,
        'caption' => explode("||",$finalRez)[0]."\n".explode("||",$finalRez)[1],
        'parse_mode' => 'HTML',
    ]);
});

Route::get('/makemoon', function (Request $request) {

    $mainChanel = 4;
    $draftPosts = \App\Models\Post::where('status', 'draft')
        ->where('channel_id', $mainChanel)
        ->where('image', 'like', '%bot/m%')
        ->orderBy('published_at', 'DESC')
        ->get();
    $limitPre = 3;

    if(sizeof($draftPosts)<$limitPre){

        $lastTime = @$draftPosts[0]->published_at;

        $curDate = @$lastTime ? Carbon::parse($lastTime) : Carbon::now();
        $h = 1;
        $date = Carbon::parse($curDate);
        $publishTime = Carbon::now();
        $publishTime->setTime(10, 20);
        $date->addDays($h);
        $date->setTime(10, 20);
        $newDate = $date->toDateTimeString();

        $apiA = (new \App\Models\Apiip());
        $text = date("d.m.Y H:i:s",strtotime($newDate));
        $addStr = "Сегодня:".date("d.m.Y",strtotime($newDate));
        // $addStr .= "\nЗнак китайский: ".$apiA->chineseZodiacSign(date("Y"))['name'];
        // Пост не найден, генерируйте ошибку или выполняйте нужное действие

        @$dateTime = new \DateTime('@' . time());
        $dateTime->setTimezone(new \DateTimeZone('Europe/Kiev'));
        $moonData = $apiA->calculateMoonPhase((strtotime($text) + $dateTime->getOffset()));
        $addStr .= "\n День луны: ".round($moonData['moonAgeDays']);
        $addStr .= "\n Фаза луны: ".($moonData['moonPhase']);

        $response = $apiA->ChatGpt("Опираясь на предоставленные данные, напиши пост с точки зрения астрологии об этом дне. Так же используй смайлики, иконки. Используй от 100 до 150 слов",$addStr);
        $finalRez = $response->choices[0]->message->content;




        $post = new \App\Models\Post();
        $post->title = "Месяц для:".$text;
        $post->content = $finalRez;
        $post->channel_id = $mainChanel;
        $post->image = 'https://mil-soft.com/1bot/m/'.((empty(round($moonData['moonAgeDays'])))?1:round($moonData['moonAgeDays'])).'.jpg';
        $post->status = 'draft';
        $post->published_at = $newDate;
        $post->save();
        // UKR
        $post = new \App\Models\Post();
        $post->title = "Месяц для:".$text;
        $post->content = $apiA->ChatGpt("Сделай перевод на Украинский",$finalRez)->choices[0]->message->content;
        $post->channel_id = 3;
        $post->image = 'https://mil-soft.com/1bot/m/'.((empty(round($moonData['moonAgeDays'])))?1:round($moonData['moonAgeDays'])).'.jpg';
        $post->status = 'draft';
        $post->published_at = $newDate;
        $post->save();
        // ENG
        $post = new \App\Models\Post();
        $post->title = "Месяц для:".$text;
        $post->content = $apiA->ChatGpt("Сделай перевод на Английский",$finalRez)->choices[0]->message->content;
        $post->channel_id = 2;
        $post->image = 'https://mil-soft.com/1bot/m/'.((empty(round($moonData['moonAgeDays'])))?1:round($moonData['moonAgeDays'])).'.jpg';
        $post->status = 'draft';
        $post->published_at = $newDate;
        $post->save();

    }
});
Route::get('/makepost', function (Request $request) {
    $currentDate = Carbon::now();
    $currentDate->setTimezone(new \DateTimeZone('Europe/Kiev'));
    $posts = \App\Models\Post::where('status', 'draft')
        ->where('published_at', '<', $currentDate)
        ->orderBy('published_at', 'ASC')
        ->get();
//dd($currentDate);
    if (sizeof($posts)>0) {
        $telegramBotToken = env('TELEGRAM_BOT_TOKEN');
        $telegram = new \Telegram\Bot\Api($telegramBotToken);

    foreach ($posts as $post){

        $chat_id = \App\Models\Channel::find($post->channel_id)->channel;
        if($post->image){
           // $inputFile = \Telegram\Bot\FileUpload\InputFile::create(($post->image), $post->image);
            $context = stream_context_create([
                'http' => [
                    'header' => "User-Agent: MyBot/1.0\r\n",
                ],
            ]);

// Fetch the image content from the URL
            $imageContent = file_get_contents($post->image, false, $context);

            if ($imageContent !== false) {
                // Generate a unique filename for the saved image
                $fileName = 'tmp.jpg'; // You can choose a different extension based on the image type
                $savePath = '/var/www/mil-soft.com/1bot/storage/app/public/' . $fileName;
                file_put_contents($savePath, $imageContent);
                $inputFile = \Telegram\Bot\FileUpload\InputFile::create($savePath);


           // dd($inputFile);
            if(mb_strlen($post->content)>1000) {
                $response = $telegram->sendPhoto([
                    'chat_id' => $chat_id,
                    'photo' => $inputFile,
                    'caption' => ((strpos($post->content,"++ASTRO++"))?explode("++ASTRO++",$post->content)[0]:''),
                    'parse_mode' => 'HTML',
                ]);
                $response = $telegram->sendMessage([
                    'chat_id' => $chat_id,
                    'text' => ((strpos($post->content,"++ASTRO++"))?explode("++ASTRO++",$post->content)[1]:$post->content),
                    'parse_mode' => 'HTML',
                ]);
            }else {
                $response = $telegram->sendPhoto([
                    'chat_id' => $chat_id,
                    'photo' => $inputFile,
                    'caption' => $post->content,
                    'parse_mode' => 'HTML',
                ]);
            }
                unlink($savePath);
            }
        }else {
        $response = $telegram->sendMessage([
            'chat_id' => $chat_id,
            'disable_web_page_preview' => false,
            'text' => $post->content,
            'parse_mode' => 'HTML',
        ]);
        }
        $messageId = $response->getMessageId();
        $savePost = \App\Models\Post::find($post->id);
        $savePost->status = 'published';
        $savePost->title = $post->title.'||'.$messageId;
        $savePost->save();
    }


    } else {


        return false;
    }
});
Route::get('/maketask', function (Request $request) {
    $limitCount = 2;

    $tasks = \App\Models\Task::whereRaw("(SELECT COUNT(*) FROM posts WHERE posts.channel_id = tasks.main_channel AND posts.status = 'draft') < ?", [$limitCount])->get();

    if(sizeof($tasks)>0){
    $apiA = (new \App\Models\Apiip());

    $response = $apiA->ChatGpt($tasks[0]->description,$tasks[0]->query);

    $finalRez = $response->choices[0]->message->content;

    if(!empty($finalRez)){
        $curDate = $tasks[0]->last_published_at ? Carbon::parse($tasks[0]->last_published_at) : Carbon::now();
        $h = $tasks[0]->task_frequency_hours;
        $date = Carbon::parse($curDate);


// Проверяем, указано ли время публикации в задаче
        if ($tasks[0]->published_time) {
            // Разбираем время публикации
            list($hours, $minutes) = explode(':', $tasks[0]->published_time);

            // Создаем объект Carbon для времени публикации
            $publishTime = Carbon::now();
            $publishTime->setTime($hours, $minutes);

            // Если время публикации меньше текущего времени, добавляем 1 день
                $date->addDays($h);

            // Устанавливаем время публикации в результате
            $date->setTime($hours, $minutes);
        }else {
            $date->addHours($h);
        }

        $newDate = $date->toDateTimeString();
        //
        $post = new \App\Models\Post();
        $post->title = "Постк к задаче №".$tasks[0]->id;
        $post->content = $finalRez;
        $post->channel_id = $tasks[0]->main_channel;
        $post->image = '';
        $post->status = 'draft';
        $post->published_at = $newDate;
        // Другие поля по вашему усмотрению

        $post->save();

        $task = \App\Models\Task::find($tasks[0]->id);
        $task->last_published_at = $newDate;
        $task->save();

    }
    }else {
        return ['count'=>0];
    }

});

Route::get('/makebio', function (Request $request) {
    $mainChanel = 4;
    $draftPosts = \App\Models\Post::where('status', 'draft')
        ->where('channel_id', $mainChanel)
        ->where('title', 'like', '%ерсоналии%')
        ->orderBy('published_at', 'DESC')
        ->get();
    $limitPre = 3;

    if(sizeof($draftPosts)<$limitPre) {
        $randomPerson = \App\Models\Person::where('pop_index', '>', 20)
            ->where('birthdate', '!=', '1970-01-01')
            ->where('pop_index', '<', 40)
            ->where('country', 'not like', '%СССР%')
            ->inRandomOrder()
            ->first();

        if ($randomPerson) {
            // URL API MediaWiki

            $pageTitle = urlencode($randomPerson->name);
            // echo $randomPerson->name.'|';
// URL страницы Википедии
            $url = $randomPerson->url;
            // Инициализируем cURL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            // Выполняем GET-запрос и получаем содержимое страницы
            $response = curl_exec($ch);

            // Закрываем cURL
            curl_close($ch);

            if (!$response) {
                return false;
            }

            $dom = new \DOMDocument();
            libxml_use_internal_errors(true);

            // Загружаем HTML в объект DOMDocument с учетом кодировки
            $dom->loadHTML(mb_convert_encoding($response, 'HTML-ENTITIES', 'UTF-8'));

            // Создаем экземпляр DOMXPath
            $xpath = new \DOMXPath($dom);

            $dataPairs = [];

            // Находим все элементы tr внутри таблицы с классом 'infobox'
            $query = "//table//img";
            $elements = $xpath->query($query);
            $photo = '';
            foreach ($elements as $element) {
                // Извлекаем текст из элементов th и td
                $src = $element->getAttribute('src');
                if (strpos($src, "upload.wiki")) {
                    $photo = 'https:' . $src;
                    break;
                }

            }
//echo $photo;
            $context = stream_context_create([
                'http' => [
                    'header' => "User-Agent: MyBot/1.0\r\n",
                ],
            ]);
            $data = file_get_contents($photo, false, $context);

            if ($data) {
                $imageInfo = getimagesizefromstring($data);

                if ($imageInfo !== false) {
                    $width = $imageInfo[0];


                    if ($width > 200) {

                        $newDate = $randomPerson->birthdate;
                        $apiA = (new \App\Models\Apiip());
                        $step = "
";
                        $lang = 'ru';
                        $text = date("d.m.Y", strtotime($newDate));
                        $addStr = "";


                        $numberBild = 'Сумма даты: <b>' . $apiA->numerologySum($text) . "=" . $apiA->numerologySum($text, 1) . '</b> ' . $step . '<i>' . $apiA->getNumSince($apiA->numerologySum($text, 1), $lang) . '</i>' . $step;

                        $numberBildClear = 'Имя: ' . $randomPerson->name . '' . $step . 'Дата рождения:' . $text . '' . $step . 'Сумма: <b>' . $apiA->numerologySum($text) . "=" . $apiA->numerologySum($text, 1) . '</b> ' . $step;

                        if ($apiA->isDate($text) && mb_strlen($text) > 4) {
                            $numberBild .= "Сумма дня: <b>" . $apiA->numerologySum(date("d", strtotime($text))) . '</b> (<i>' . $apiA->getNumSince($apiA->numerologySum(date("d", strtotime($text)), 1), $lang) . '</i>)' . $step;
                            $numberBildClear .= "День: <b>" . $apiA->numerologySum(date("d", strtotime($text))) . '</b> ' . $step;
                        }
                        $fixNum = explode("=", $apiA->numerologySum($text))[1];
                        if ($fixNum > 10 && $fixNum < 34) {
                            if (in_array($fixNum, $apiA->superNum)) {
                                $numberBild .= 'Мастер число: <b>' . $fixNum . '</b> (<i>' . $apiA->getNumSince($fixNum, $lang) . '</i>)' . $step;
                                $numberBildClear .= 'Мастер число: <b>' . $fixNum . '</b>' . $step;
                            }
                        }

                        $final = strip_tags($numberBild . $addStr);


                        $imageUrl = $photo;
                        $req = 'Опираясь на предоставленные расчеты, напиши пост о '.$randomPerson->name.' ('.$randomPerson->url.') с точки зрения нумерологии, используй факты из биографии. Так же используй смайлики, иконки';
                        $response = $apiA->ChatGpt($req, $final);
                        $finalRez = $numberBildClear . "++ASTRO++" . $response->choices[0]->message->content;

                        if (!empty($finalRez)) {
                            $lastTime = @$draftPosts[0]->published_at;
                            $curDate = @$lastTime ? Carbon::parse($lastTime) : Carbon::now();
                            $h = 1;
                            $date = Carbon::parse($curDate);
                            $publishTime = Carbon::now();
                            $publishTime->setTime(16, 05);
                            $date->addDays($h);
                            $date->setTime(16, 05);
                            $newDate = $date->toDateTimeString();

                            $post = new \App\Models\Post();
                            $post->title = "Персоналии...";
                            $post->content = $finalRez;
                            $post->channel_id = $mainChanel;
                            $post->image = $imageUrl;
                            $post->status = 'draft';
                            $post->published_at = $newDate;
                            $post->save();
                            // UKR
                            $post = new \App\Models\Post();
                            $post->title = "Персоналии...";
                            $post->content = $apiA->ChatGpt("Сделай перевод на Украинский",$finalRez)->choices[0]->message->content;
                            $post->channel_id = 3;
                            $post->image = $imageUrl;
                            $post->status = 'draft';
                            $post->published_at = $newDate;
                            $post->save();
                            // ENG
                            $post = new \App\Models\Post();
                            $post->title = "Персоналии...";
                            $post->content = $apiA->ChatGpt("Сделай перевод на Английский",$finalRez)->choices[0]->message->content;
                            $post->channel_id = 2;
                            $post->image = $imageUrl;
                            $post->status = 'draft';
                            $post->published_at = $newDate;
                            $post->save();
                        }
                    }

                } else {
                    return "Не удалось получить информацию о размерах изображения.";
                }
            } else {
                return "Изображение недоступно по указанному URL.";
            }


            // В $randomPerson будет случайная запись, удовлетворяющая заданным условиям.
            // Можете выполнять нужные действия с этой записью.
        } else {
            // Если не найдено записей, удовлетворяющих условиям, то выполните необходимую обработку.
        }
    }
});
Route::get('/testt', function (Request $request) {
    $apiA = (new \App\Models\Apiip());
    $telegramBotToken = env('TELEGRAM_BOT_TOKEN');
    $telegram = new \Telegram\Bot\Api($telegramBotToken);


    $imageUrl = 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/33/Rita_Hayworth-publicity.JPG/274px-Rita_Hayworth-publicity.JPG';
    $inputFile = \Telegram\Bot\FileUpload\InputFile::create(($imageUrl), $imageUrl);
//    $req = "Опираясь на имя человека, напиши пост об этом человеке. Так же используй смайлики, иконки";
//    $response = $apiA->ChatGpt($req,"Ри́та Хе́йворт");
    //print_r($response);
    $finalRez = "🌙 Сегодня, 19 октября 2023 года, - особый день для астрологии, так как наступил период Новолуния! Это идеальное время для постановки новых целей, начала проектов и преображений в жизни. Луна в этот день приносит силу и энергию, способствующую росту и развитию.💪

❤️ Под влиянием Новолуния, открывается возможность привлечь любовь и гармонию в отношениях. Это момент, когда можно обратить внимание на свои эмоции и проявить искреннюю заботу о близких. 🥰

💼 В бизнесе и карьере Новолуние приносит с собой рост и новые возможности. Это время, чтобы прокачать свои навыки, улучшить свою производительность и стремиться к большему успеху. 💼

🧘‍♀️ День Новолуния также предлагает возможность сосредоточиться на своем внутреннем мире. Медитация, йога или другие виды саморазвития помогут обрести гармонию и внутренний покой. 🌟

🌙 Итак, сегодня - идеальный день для запуска новых начинаний и преобразований. Воспользуйтесь нашей мощной Луны, чтобы достичь всех своих целей! ✨😊";
echo mb_strlen($finalRez).'--';
    if(mb_strlen($finalRez)>1000) {
        $response = $telegram->sendPhoto([
            'chat_id' => '@helloastro',
            'photo' => $inputFile,
            'caption' => '',
        ]);
        $response = $telegram->sendMessage([
            'chat_id' => '@helloastro',
            'text' => $finalRez,
            'parse_mode' => 'HTML',
        ]);
    }else {
        $response = $telegram->sendPhoto([
            'chat_id' => '@helloastro',
            'photo' => $inputFile,
            'caption' => $finalRez,
        ]);
    }
    $messageId = $response->getMessageId();
    dd($messageId);


    $step="
";
    $lang = 'ru';
    $text = date("d.m.Y");
    $addStr = "";





    $numberBild = 'Сумма: <b>'.$apiA->numerologySum($text)."=".$apiA->numerologySum($text,1).'</b> '.$step.'<i>'.$apiA->getNumSince( $apiA->numerologySum($text,1) ,$lang).'</i>'.$step;

    $numberBildClear = date("d.m.Y").''.$step.'Сумма: <b>'.$apiA->numerologySum($text)."=".$apiA->numerologySum($text,1).'</b> '.$step;

    if($apiA->isDate($text)&&mb_strlen($text)>4) {
        $numberBild .= "День: <b>" . $apiA->numerologySum(date("d", strtotime($text))).'</b> (<i>'.$apiA->getNumSince($apiA->numerologySum(date("d", strtotime($text)),1),$lang).'</i>)'.$step;
        $numberBildClear .= "День: <b>" . $apiA->numerologySum(date("d", strtotime($text))).'</b> '.$step;
    }
    $fixNum = explode("=",$apiA->numerologySum($text))[1];
    if($fixNum>10&&$fixNum<34){
        if(in_array($fixNum,$apiA->superNum)){
            $numberBild .= 'Мастер: <b>'.$fixNum.'</b> (<i>'.$apiA->getNumSince($fixNum,$lang).'</i>)'.$step;
            $numberBildClear .= 'Мастер: <b>'.$fixNum.'</b>'.$step;
        }
    }

    $final = strip_tags($numberBild.$addStr);

    $req = "Опираясь на предоставленные данные, напиши пост с точки зрения нумерологии об этом дне. Так же используй смайлики, иконки";
    $response = $apiA->ChatGpt($req,$final);

    //print_r($response);
    $finalRez = $numberBildClear.$response->choices[0]->message->content;
//dd($finalRez);
// Создайте объект InputFile
    $imageUrl = 'https://mil-soft.com/1bot/img/main-notebook.png';
    $inputFile = \Telegram\Bot\FileUpload\InputFile::create(($imageUrl), 'https://mil-soft.com/1bot/img/main-notebook.png');
    print_r(mb_strlen($finalRez));
    $response = $telegram->sendMessage([
        'chat_id' => '@helloastro',
        'disable_web_page_preview' => false,
        'text' => $finalRez,
        'parse_mode' => 'HTML',
        //'parse_mode' => 'HTML',
    ]);

        $messageId = $response->getMessageId();
    dd($messageId);
    $chatId = '@helloastro'; // ID чата, в который вы хотите отправить опрос
    $question = 'Какой ваш любимый цвет?';
    $options = ['Красный', 'Зеленый', 'Синий', 'Другой'];

    $response = $telegram->sendPoll([
        'chat_id' => $chatId,
        'question' => $question,
        'options' => $options,
    ]);

// После создания опроса, вы можете получить информацию о нем из ответа
    $pollId = $response->getPoll()->getId();
    echo "ID опроса: " . $pollId;
});
Route::get('/img', function (Request $request) {

    $mainChanel = 4;
    $draftPosts = \App\Models\Post::where('status', 'draft')
        ->where('channel_id', $mainChanel)
        ->where('title', 'like', '%невная нумерология%')
        ->orderBy('published_at', 'DESC')
        ->get();
    $limitPre = 3;

    if(sizeof($draftPosts)<$limitPre) {
        $lastTime = @$draftPosts[0]->published_at;

        $curDate = @$lastTime ? Carbon::parse($lastTime) : Carbon::now();
        $h = 1;
        $date = Carbon::parse($curDate);
        $publishTime = Carbon::now();
        $publishTime->setTime(12, 00);
        $date->addDays($h);
        $date->setTime(12, 00);
        $newDate = $date->toDateTimeString();

        $apiA = (new \App\Models\Apiip());
        $step = "
";
        $lang = 'ru';
        $text = date("d.m.Y",strtotime($newDate));
        $addStr = "";


        $numberBild = 'Сумма даты: <b>' . $apiA->numerologySum($text) . "=" . $apiA->numerologySum($text, 1) . '</b> ' . $step . '<i>' . $apiA->getNumSince($apiA->numerologySum($text, 1), $lang) . '</i>' . $step;

        $numberBildClear = $text . '' . $step . 'Сумма: <b>' . $apiA->numerologySum($text) . "=" . $apiA->numerologySum($text, 1) . '</b> ' . $step;

        if ($apiA->isDate($text) && mb_strlen($text) > 4) {
            $numberBild .= "Сумма дня: <b>" . $apiA->numerologySum(date("d", strtotime($text))) . '</b> (<i>' . $apiA->getNumSince($apiA->numerologySum(date("d", strtotime($text)), 1), $lang) . '</i>)' . $step;
            $numberBildClear .= "День: <b>" . $apiA->numerologySum(date("d", strtotime($text))) . '</b> ' . $step;
        }
        $fixNum = explode("=", $apiA->numerologySum($text))[1];
        if ($fixNum > 10 && $fixNum < 34) {
            if (in_array($fixNum, $apiA->superNum)) {
                $numberBild .= 'Мастер число: <b>' . $fixNum . '</b> (<i>' . $apiA->getNumSince($fixNum, $lang) . '</i>)' . $step;
                $numberBildClear .= 'Мастер число: <b>' . $fixNum . '</b>' . $step;
            }
        }

        $final = strip_tags($numberBild . $addStr);

        $req = "Опираясь на предоставленные данные, напиши пост с точки зрения нумерологии об этом дне. Так же используй смайлики, иконки";
        $response = $apiA->ChatGpt($req, $final);

        //print_r($response);
        $finalRez = $numberBildClear . $response->choices[0]->message->content;
        if (!empty($finalRez)) {


            $post = new \App\Models\Post();
            $post->title = "Дневная нумерология:".$text;
            $post->content = $finalRez;
            $post->channel_id = $mainChanel;
            $post->image = '';
            $post->status = 'draft';
            $post->published_at = $newDate;
            $post->save();
            // UKR
            $post = new \App\Models\Post();
            $post->title = "Дневная нумерология:".$text;
            $post->content = $apiA->ChatGpt("Сделай перевод на Украинский",$finalRez)->choices[0]->message->content;
            $post->channel_id = 3;
            $post->image = '';
            $post->status = 'draft';
            $post->published_at = $newDate;
            $post->save();
            // ENG
            $post = new \App\Models\Post();
            $post->title = "Дневная нумерология:".$text;
            $post->content = $apiA->ChatGpt("Сделай перевод на Английский",$finalRez)->choices[0]->message->content;
            $post->channel_id = 2;
            $post->image = '';
            $post->status = 'draft';
            $post->published_at = $newDate;
            $post->save();

//            $telegramBotToken = env('TELEGRAM_BOT_TOKEN');
//            $telegram = new \Telegram\Bot\Api($telegramBotToken);
//
//// URL изображения
//            $imageUrl = 'https://mil-soft.com/1bot/img/main-notebook.png';
//
//// Создайте объект InputFile
//            $inputFile = \Telegram\Bot\FileUpload\InputFile::create(($imageUrl), 'custom_filename.jpg');
//
//            $response = $telegram->sendMessage([
//                'chat_id' => '@astronum33ru',
//                //'photo' => $inputFile,
//                'text' => $finalRez,
//                'parse_mode' => 'HTML',
//            ]);
//
//            $response = $telegram->sendMessage([
//                'chat_id' => '@astronum33ua',
//                //'photo' => $inputFile,
//                'text' => $apiA->ChatGpt("Сделай перевод на Украинский", $finalRez)->choices[0]->message->content,
//                'parse_mode' => 'HTML',
//            ]);
//
//            $response = $telegram->sendMessage([
//                'chat_id' => '@astronum33',
//                // 'photo' => $inputFile,
//                'text' => $apiA->ChatGpt("Сделай перевод на Английский", $finalRez)->choices[0]->message->content,
//                'parse_mode' => 'HTML',
//            ]);

        }

    }
});


Route::get('/papi', function (Request $request) {
// Категория "Персоналии по алфавиту"
    $categoryTitle = "Категория:Персоналии_по_алфавиту";

// URL API MediaWiki
    $apiUrl = "https://ru.wikipedia.org/w/api.php";

// Параметры запроса для получения списка статей в категории
    $params = array(
        "action" => "query",
        "format" => "json",
        "list" => "categorymembers",
        "cmlimit" => 1000,
        "cmtitle" => $categoryTitle,
    );

// Формирование URL запроса
    $queryUrl = $apiUrl . "?" . http_build_query($params);

// Выполнение запроса и получение ответа
    $response = file_get_contents($queryUrl);

// Преобразование JSON-ответа в массив
    $data = json_decode($response, true);
dd($data);
// Извлечение списка статей
    if (isset($data["query"]["categorymembers"])) {
        $categoryMembers = $data["query"]["categorymembers"];

        foreach ($categoryMembers as $member) {
            echo $member["title"] . "<br>";
        }
    } else {
        echo "Страницы в категории не найдены.";
    }
});
Route::get('/bin', function (Request $request) {
$baseUrl = "https://api.binance.com/api/v3/depth";
$symbol = "BTCUSDT";

// Создаем URL для запроса
$url = "{$baseUrl}?symbol={$symbol}";

// Выполняем GET-запрос
$response = file_get_contents($url);

if ($response === false) {
    echo "Ошибка при выполнении запроса.";
} else {
    // Преобразуем JSON-ответ в массив
    $orderBook = json_decode($response, true);

    if (is_array($orderBook)) {
        // Суммируем объемы ордеров на покупку и продажу
        $totalBidVolume = 0;
        $totalAskVolume = 0;

        foreach ($orderBook['bids'] as $bid) {
            $totalBidVolume += $bid[1];
        }

        foreach ($orderBook['asks'] as $ask) {
            $totalAskVolume += $ask[1];
        }

        echo "Общий объем ордеров на покупку: {$totalBidVolume} BTC\n";
        echo "Общий объем ордеров на продажу: {$totalAskVolume} BTC\n";
    } else {
        echo "Ошибка при обработке данных.";
    }
}
});
Route::get('/pp', function (Request $request) {
// Пример использования
    $person = new \App\Models\Person();
    if($request->id){
        $url = $person->find($request->id)->url;
        $data = $person->parseWikipediaPage($url);

        $person = \App\Models\Person::find($request->id); // Находим модель Person по ID 5

        if ($person) {

            // Обновляем данные модели на основе предоставленного массива
            if(!empty( $data['birthdate'])){
            $person->birthdate = Carbon::createFromFormat('d.m.Y', $data['birthdate'])->format('Y-m-d');
            $person->date_of_death = ($data['date_of_death'])?Carbon::createFromFormat('d.m.Y', $data['date_of_death'])->format('Y-m-d'):null;
            $person->country = $data['country'];
            $person->death_country = $data['death_country'];
            $person->profession = ($data['profession']);
            $person->status = $data['status'];
            $person->zodiac_index = $data['zodiac_index'];
            $person->chinese_zodiac_index = $data['chinese_zodiac_index'];
            $person->birthdate_number = $data['birthdate_number'];
            $person->death_date_number = $data['death_date_number'];
            $person->day_of_month = $data['day_of_month'];
            $person->pop_index = $data['pop_index'];
            $person->moon_day = $data['moon_day'];
            }else {
                $person->status = 2;
            }
            // Сохраняем обновленные данные в базу данных
            $person->save();
        }
    }else {
        $people = \App\Models\Person::where('status','')->orderBy('id', 'desc')->take(10)->get();
        foreach ($people as $person) {
            $data = $person->parseWikipediaPage($person->url);
            $personS = \App\Models\Person::find($person->id);
            $maxCountryLength = 255; // Примерная максимальная длина, замените на фактическую

           // $shortenedCountry = mb_substr($country, 0, $maxCountryLength);
            if(@mb_strlen($data['profession'])>250){ $data['profession'] = mb_substr($data['profession'], 0, $maxCountryLength); }
            if(@mb_strlen($data['country'])>250){ $data['country'] = mb_substr($data['country'], 0, $maxCountryLength); }
            if(@mb_strlen($data['death_country'])>250){ $data['death_country'] = mb_substr($data['death_country'], 0, $maxCountryLength); }


            if(!empty( $data['birthdate'])){

                $personS->birthdate = Carbon::createFromFormat('d.m.Y', $data['birthdate'])->format('Y-m-d');
                $personS->date_of_death = ($data['date_of_death'])?Carbon::createFromFormat('d.m.Y', $data['date_of_death'])->format('Y-m-d'):null;
                $personS->country = $data['country'];
                $personS->death_country = $data['death_country'];
                $personS->profession = $data['profession'];
                $personS->status = $data['status'];
                $personS->zodiac_index = $data['zodiac_index'];
                $personS->chinese_zodiac_index = $data['chinese_zodiac_index'];
                $personS->birthdate_number = $data['birthdate_number'];
                $personS->death_date_number = $data['death_date_number'];
                $personS->day_of_month = $data['day_of_month'];
                $personS->pop_index = $data['pop_index'];
                $personS->moon_day = $data['moon_day'];
            }else {
                $personS->status = 2;
            }
            // Сохраняем обновленные данные в базу данных
            $personS->save();
        }
   // $url = 'https://ru.wikipedia.org/wiki/%D0%9A%D0%B0%D1%82%D1%8B%D1%81,_%D0%93%D0%B5%D0%BE%D1%80%D0%B3%D0%B8%D0%B9_%D0%9F%D0%B5%D1%82%D1%80%D0%BE%D0%B2%D0%B8%D1%87';
   //     $data = $person->parseWikipediaPage($url);
    }



    if ($data) {
       // print_r($data);
    } else {
        echo 'Не удалось получить данные.';
    }
});

Route::get('/parse', function (Request $request) {
  // return \App\Models\Person::all();
    $latestPerson = \App\Models\Person::orderBy('id', 'desc')->first(); // Получаем последнюю запись

    if ($latestPerson) {
        $lastName = $latestPerson->name; // Получаем имя
        $person = new \App\Models\Person();
        $person->parseAndCreate($lastName);

        echo "Имя последней записи: $lastName";
    } else {

        $person = new \App\Models\Person();
        $person->parseAndCreate('');
    }


    echo 'Всего:'.\App\Models\Person::count();
    exit;
});



Route::get('/user', function (Request $request) {

        $thisUser = @\App\Models\Employee::where(['token'=>$request->token])->get()[0];
  //  dd($thisUser[0]->id);
        if(@$thisUser->id){

            return [
                'data'=> $thisUser,
                'status'=>200
            ];
        }else {
        return [
            'text'=> 'Ошибка - такого пользователя нет в базе',
            'status'=>500
        ];
    }
});


Route::get('/finance', function (Request $request) {

    $thisUser = @\App\Models\Employee::where(['token'=>$request->token])->get()[0];
    //  dd($thisUser[0]->id);
    if(@$thisUser->id){

        return [
            'data'=> \App\Models\Finance::where('employees_id',$thisUser->id)->get(),
            'status'=>200
        ];
    }else {
        return [
            'text'=> 'Ошибка - такого пользователя нет в базе',
            'status'=>500
        ];
    }
});


Route::get('/country', function (Request $request) {

    $thisUser = @\App\Models\Employee::where(['token'=>$request->token])->get()[0];
    //  dd($thisUser[0]->id);
    if(@$thisUser->id){

        return [
            'data'=> \App\Models\Country::all(),
            'status'=>200
        ];
    }else {
        return [
            'text'=> 'Ошибка - такого пользователя нет в базе',
            'status'=>500
        ];
    }
});

Route::get('/wslink', function (Request $request) {

    $thisUser = @\App\Models\Employee::where(['token'=>$request->token])->get()[0];
    //  dd($thisUser[0]->id);
    if(@$thisUser->id){

        return [
            'data'=> \App\Models\Wslink::all(),
            'status'=>200
        ];
    }else {
        return [
            'text'=> 'Ошибка - такого пользователя нет в базе',
            'status'=>500
        ];
    }
});

Route::get('/ws', function (Request $request) {

    $thisUser = @\App\Models\Employee::where(['token'=>$request->token])->get()[0];
    //  dd($thisUser[0]->id);
    if(@$thisUser->id){

        return [
            'data'=> \App\Models\Webservices::all(),
            'status'=>200
        ];
    }else {
        return [
            'text'=> 'Ошибка - такого пользователя нет в базе',
            'status'=>500
        ];
    }
});