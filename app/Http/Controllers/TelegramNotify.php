<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Telegram\Bot\Api;

class TelegramNotify extends Controller
{
    

    //send
    public static function T_NOTIFY($msg=null, $chat_id=null){
        // dd('ok');

        if(!$chat_id){
            $chat_id = config('values.telegram_chat_id');
        }
     
       // CPB-IT
       $telegram = new Api('7240400341:AAE2reMkathlbMljuGcBwOR5joWyOCOT1Q4');

       // CPB-IT-Test 
       // $telegram = new Api('7718592225:AAGLXxcQ7ZW_c2FmYE8NEgguHQaMxQOEU1E');

       if(!$msg){
          $msg= "This is a test SMS \n\n\n <i>Italic</i> \n <u>Underline</u> \n <s>Strikethrough</s> \n\n <pre>code block</pre> \n\n <a href='https://it.cpbangladesh.com/login'>CPB-IT Login</a> \n\n 😊 😎 😄 — Smiling faces \n\n 🎉 🎊 ✨ — Celebration \n\n 🔥 🚀 🌟 — Excitement or success \n\n ✅ ❌ ⚠️ — Checkmarks and warnings";
       }

        // dd($telegram);
        
        // $response = $telegram->getMe();
        // $botId = $response->getId();
        // $firstName = $response->getFirstName();
        // $username = $response->getUsername();
        // $getUpdates = $telegram->getUpdates();

    
        $response = $telegram->sendMessage([
            'chat_id' => $chat_id, 
            'text' => $msg,
            'parse_mode' => 'HTML'
        ]);
        
        if(config('values.app_debug')){
            return $response;
        }

        return true;

      
    }




        
    //     Smileys & Emotion
    // 😀 Grinning Face
    // 😃 Grinning Face with Big Eyes
    // 😄 Grinning Face with Smiling Eyes
    // 😁 Beaming Face with Smiling Eyes
    // 😆 Grinning Squinting Face
    // 😅 Grinning Face with Sweat
    // 😂 Face with Tears of Joy
    // 🤣 Rolling on the Floor Laughing
    // 😊 Smiling Face with Smiling Eyes
    // 😇 Smiling Face with Halo
    // 🙂 Slightly Smiling Face
    // 🙃 Upside-Down Face
    // 😉 Winking Face
    // 😍 Smiling Face with Heart-Eyes
    // 😘 Face Blowing a Kiss
    // 😗 Kissing Face
    // 😚 Kissing Face with Closed Eyes
    // 😋 Face Savoring Food
    // 😜 Winking Face with Tongue
    // 😝 Squinting Face with Tongue
    // 😏 Smirking Face
    // 😒 Unamused Face
    // 😔 Pensive Face
    // 😓 Downcast Face with Sweat
    // 😢 Crying Face
    // 😭 Loudly Crying Face
    // 😤 Face with Steam From Nose
    // 😡 Pouting Face
    // 😱 Face Screaming in Fear
    // 🥺 Pleading Face
    // 😳 Flushed Face
    // 😬 Grimacing Face
    // 🤔 Thinking Face
    // 🤗 Hugging Face
    // People & Body
    // 👋 Waving Hand
    // 🤚 Raised Back of Hand
    // 🖐️ Hand with Fingers Splayed
    // ✋ Raised Hand
    // 🖖 Vulcan Salute
    // 👌 OK Hand
    // 🤌 Pinched Fingers
    // 🤏 Pinching Hand
    // ✌️ Victory Hand
    // 🤞 Crossed Fingers
    // 🤟 Love-You Gesture
    // 🤙 Call Me Hand
    // 👈 Backhand Index Pointing Left
    // 👉 Backhand Index Pointing Right
    // 👆 Backhand Index Pointing Up
    // 👇 Backhand Index Pointing Down
    // 👍 Thumbs Up
    // 👎 Thumbs Down
    // 👊 Oncoming Fist
    // 🤛 Left-Facing Fist
    // 🤜 Right-Facing Fist
    // 🤲 Palms Up Together
    // 🙏 Folded Hands
    // 🤝 Handshake
    // 👏 Clapping Hands
    // 👐 Open Hands
    // 🙌 Raising Hands
    // 💪 Flexed Biceps
    // 🦵 Leg
    // 🦶 Foot


    // Animals & Nature
    // 🐶 Dog Face
    // 🐱 Cat Face
    // 🦊 Fox Face
    // 🐻 Bear Face
    // 🐼 Panda Face
    // 🐨 Koala
    // 🐯 Tiger Face
    // 🦁 Lion Face
    // 🐮 Cow Face
    // 🐷 Pig Face
    // 🐵 Monkey Face
    // 🐸 Frog
    // 🦄 Unicorn
    // 🐔 Chicken
    // 🐧 Penguin
    // 🐦 Bird
    // 🐤 Baby Chick
    // 🐍 Snake
    // 🐢 Turtle
    // 🐬 Dolphin
    // 🦢 Swan
    // 🐝 Bee
    // 🦋 Butterfly
    // 🌸 Cherry Blossom
    // 🌹 Rose
    // 🌻 Sunflower
    // 🍁 Maple Leaf
    // Food & Drink
    // 🍏 Green Apple
    // 🍎 Red Apple
    // 🍌 Banana
    // 🍉 Watermelon
    // 🍇 Grapes
    // 🍓 Strawberry
    // 🍒 Cherries
    // 🍍 Pineapple
    // 🥥 Coconut
    // 🍅 Tomato
    // 🥕 Carrot
    // 🥦 Broccoli
    // 🧄 Garlic
    // 🍞 Bread
    // 🧀 Cheese
    // 🍔 Hamburger
    // 🍟 French Fries
    // 🍕 Pizza
    // 🍣 Sushi
    // 🍦 Ice Cream
    // 🍩 Doughnut
    // 🍪 Cookie
    // 🎂 Birthday Cake
    // 🍫 Chocolate Bar
    // 🍿 Popcorn
    // ☕ Hot Beverage
    // 🍺 Beer Mug



    // Travel & Places
    // 🚗 Car
    // 🚕 Taxi
    // 🚌 Bus
    // 🚑 Ambulance
    // 🚒 Fire Engine
    // 🚓 Police Car
    // 🚜 Tractor
    // 🚲 Bicycle
    // 🛴 Kick Scooter
    // 🚀 Rocket
    // 🛳️ Ship
    // 🛩️ Airplane
    // 🏠 House
    // 🏢 Office Building
    // 🏨 Hotel
    // 🏦 Bank
    // 🏫 School
    // 🌍 Earth Globe Europe-Africa
    // 🌞 Sun
    // 🌝 Full Moon Face
    // 🌈 Rainbow



    // Objects
    // 🎒 Backpack
    // 💼 Briefcase
    // 🕶️ Sunglasses
    // ⏰ Alarm Clock
    // 🔑 Key
    // 🛏️ Bed
    // 📱 Mobile Phone
    // 💻 Laptop
    // 🖨️ Printer
    // 📸 Camera
    // 🎥 Movie Camera
    // 💡 Light Bulb
    // 🔦 Flashlight
    // 📚 Books
    // 🖊️ Pen
    // ✂️ Scissors
    // 🔒 Lock
    // 🔑 Key
    // 🧯 Fire Extinguisher
    // Symbols
    // ❤️ Red Heart
    // 💔 Broken Heart
    // 🔥 Fire
    // 🌟 Star
    // 💯 Hundred Points
    // ✔️ Check Mark
    // ❌ Cross Mark
    // ⚠️ Warning
    // ⛔ No Entry
    // 💲 Dollar Sign
    // ♻️ Recycling Symbol
    // 🔄 Counterclockwise Arrows
    // 💬 Speech Balloon
    // 📶 Antenna Bars

}
