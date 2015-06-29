<script type="text/javascript" charset="utf-8" src="//api-maps.yandex.ru/services/constructor/1.0/js/?sid=mSt6kl67Co-RQLHvW2pSArSIGktSudrE&width=auto&height=200"></script>

<script type="text/javascript">
   $(document).on("focus", "#contacts-holder input[name=name], #contacts-holder input[name=email], #contacts-holder input[name=tel], #contacts-holder textarea", function() {
      window.oldVal = $(this).val();
      $(this).val("");
   });

   $(document).on("blur", "#contacts-holder input[name=name], #contacts-holder input[name=email], #contacts-holder input[name=tel], #contacts-holder textarea", function() {
      var currentValue = $(this).val();
      if(currentValue == "")
      {
         $(this).val(window.oldVal);
      }
   });

   $(document).on("click", "#clear-btn", function() {
     $("#contacts-holder input[name=name], #contacts-holder input[name=email], #contacts-holder input[name=tel], #contacts-holder textarea").val("");
   });
</script>
<div id = "contacts-holder">
<div class="row">
<div class="col-md-6 col-sm-12 col-xs-12">
<div id="address-holder">
   <div class="row">
     <div class="col-md-12">
       <span id="form-title">Наш адрес</span>
     </div>
  </div>
   <div class="row address-phone">
     <div class="col-md-12">
       г.Саратов
     </div>
  </div>
 <div class="row address-phone">
     <div class="col-md-12">
       ул. Московская, 96
     </div>
  </div>
 <div class="row address-phone">
     <div class="col-md-12">
       тел.: +7 (452) 264-124, +7 (965) 882-98-78
     </div>
  </div>
 <div class="row address-time">
     <div class="col-md-12">
        часы работы: с 10:00 до 00:00
     </div>
  </div>
 <div class="row address-time">
     <div class="col-md-12">
       бронирование: с 10:00 до 12:00
     </div>
  </div>
</div>
</div>
<div class="col-md-6 col-sm-12 col-xs-12">
<div id = "contacts-form-holder">
<?php function show_form() { ?>
   <form action="" method="post">

<div class="container" style="width:100%;">
<div class="row">
  <div class="col-md-3">
     <span id="form-title">Обратная связь</span>
  </div>
  <div class="col-md-9">
  </div>
</div>
<div class="row">
  <div class="col-md-5">
     <div class="row">
        <div class="col-md-12">
          <input id="name" name="name" size="40" type="text" value = "Ваше имя" />
        </div>
     </div>
     <div class="row">
        <div class="col-md-12">
           <input id="email" name="email" size="40" type="text" value="Ваш E-mail"/>
        </div>
     </div>
     <div class="row">
        <div class="col-md-12">
           <input id="tel" name="tel" size="40" type="text" value = "Ваш номер телефона" />
        </div>
     </div>
  </div>
  <div class="col-md-7">
     <textarea id="mess" name="mess" cols="41" rows="7">Сообщение</textarea>
     <br />
     <input type="submit" value="Отправить" name="submit"/>
     <input type="button" value="Очистить" name="clear" id="clear-btn" />
  </div>
</div>
</div>
   </form>
<?
}
function complete_mail() {
   $_POST['mess']=htmlspecialchars(trim($_POST['mess']));
   $_POST['name']=htmlspecialchars(trim($_POST['name']));
   $_POST['tel']=htmlspecialchars(trim($_POST['tel']));
   $_POST['email']=htmlspecialchars(trim($_POST['email']));
   if ((empty($_POST['name']))||(!preg_match("/[0-9a-z_]+@[0-9a-z_^\.]+\.[a-z]{2,3}/i",$_POST['email']))||(empty($_POST['mess']))||(empty($_POST['tel']))) {
      $errors=array();
      if (empty($_POST['name'])) {
         $errors[]=0;
      }
      if(!preg_match("/[0-9a-z_]+@[0-9a-z_^\.]+\.[a-z]{2,3}/i",$_POST['email'])) {
         $errors[]=1;
      }
      if(empty($_POST['mess'])) {
         $errors[]=2;
      }
      if(empty($_POST['tel'])) {
         $errors[]=3;
      }
      output_err($errors);
   }
   else {
      $oCore_QueryBuilder_Select = Core_QueryBuilder::select()
         ->from('constants')
         ->where('name','=','SUPERUSER_EMAIL');
      $aRows = $oCore_QueryBuilder_Select->execute()->asAssoc()->result();
      $to=$aRows[0]['value'];
      $mess='Имя отправителя:'.$_POST['name'].'
Контактный телефон:'.$_POST['tel'].'
Контактный email:'.$_POST['email'].'
'.$_POST['mess'];
      $title=$_SERVER['HTTP_HOST']." | Обратная связь";
      $from=$_POST['email'];
      $headers="From: {$from}\r\n";
      $headers.= "MIME-Version: 1.0\r\n";
      $headers.="Content-type: text/plain; charset=utf-8\r\n";
      $headers.="Content-Transfer-Encoding: 8bit";
      mail($to,$title,$mess,$headers);
      echo 'Спасибо! Ваше письмо отправлено.';
   }
}
function output_err($errors) {
   $err[0]='ОШИБКА! Не введено имя.';
   $err[1]='ОШИБКА! Неверно введен e-mail.';
   $err[2]='ОШИБКА! Не введено сообщение.';
   $err[3]='ОШИБКА! Не введен телефон.';
   foreach ($errors as $error) {
      echo '<p class="mess_err">'.$err[$error].'</p>';
   }
   show_form();
}
if (isset($_POST['submit'])) {
   complete_mail();
}
else {
   show_form();
}
?>
</div>
</div>
</div>
<!-- Usage as a class -->
<div class="clearfix"></div>
</div>