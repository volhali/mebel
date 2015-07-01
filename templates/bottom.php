        </div>
        <div class="right">
        <h2>Новые услуги</h2>
          <?
          $query = "SELECT * FROM $tbl_maintext where status = 'writetop'";
         $new1= mysql_query($query);
         if(!$new1){
          exit($query);
         }
         while($text1 = mysql_fetch_array($new1)){
              echo "<p><strong>".$text1['name']."</strong></p>";
              echo "<p>".$text1['smbody'];
             echo" <a href = 'comment.php?id=".$text1['id']."'>Комментарий</a>"."</p>";
         }//выводит ассоциативный массив

         ?>

        </p>
        <h2>Изготовление мебели<br />
          <span> Иготовим в короткие сроки</span></h2>
        <p><strong>2012</strong><br />
          Мы изготавливаем мебель из дуба, сосны, лиственницы, других пород дерева. Вы можете выбрать любой понравившийся по своей текстуре и цвету сорт древесины.</p>
        <p><strong>2012</strong><br />
В воссоздании антикварной или старинной модели по фотографии, мы применяем технологии старения, – патинирование, кракле. В создании уникальной модели мебели помогут наши дизайнеры.</p>
        <p><strong>2012</strong><br />
          Вы сможете проконтролировать весь процесс изготовления мебели: с момента проектирования до сборки высококлассных комплектующих, соавторами которых будете Вы сами.</p>
        <div class="bg"></div>
        <p><a href="#">+ Дополнительно</a></p>
      </div>
    
      <div class="clr"></div>
    </div>
    <div class="clr"></div>
  </div>
  <div class="footer">
    <div class="footer_resize">
      <p class="leftt">SiteY.ru &copy; 2013 </p>

      <div class="clr"></div>
    </div>
  </div>                              
</div>
<!-- Конец -->
                                                                                                                                                                                                                                                                                                       <p align="right"><a target="_blank" style="text-decoration: none; color: #0095F9" href="http://sitey.ru/"> Личный сайт</a> &nbsp; &nbsp; &nbsp; </p>
</body>
</html>