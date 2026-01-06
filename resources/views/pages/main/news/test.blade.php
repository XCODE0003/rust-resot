@extends('layouts.main')
@section('title', 'T
est News' )

@section('content')


<div class="inner-header">Заголовок</div>
<div class="inner">
  <div class="container">
    <div class="back__text">
      <a href="#"> <i class="fa-solid fa-arrow-left"></i>Назад </a>
    </div>
    <div class="open-new text-page" using-lightbox>
      <!-- main-title -->
      <h1 class="main-h">
        <span>-</span>
        ТЕКСТОВАЯ СТРАНИЦА
      </h1>
      <!-- img -->
      <div class="big-img edit-block" blocktype="image" data-image-style="none" contenteditable="false">
        <img src="/images/text-img.jpg" alt="text-img" class="lightbox-thumbnail"/>
      </div>

      <!-- title -->
      <h1>ЭТО H1 ЗАГОЛОВОК!</h1>
      <p>
        Lorem ipsum, dolor sit amet consectetur adipisicing elit. Voluptate
        dolores placeat earum laudantium doloremque facilis id blanditiis enim
        voluptatibus consequatur nulla modi consequuntur, ex eveniet pariatur ab
        aliquam. Maxime, alias.
      </p>
      <p>
        Lorem ipsum dolor sit amet consectetur adipisicing elit. Impedit
        temporibus, sint doloremque aspernatur quisquam error amet. Modi
        expedita, quas reiciendis quia nesciunt eaque aliquid, velit debitis id
        quibusdam explicabo vitae.
      </p>
      <h2>ЭТО H2 ЗАГОЛОВОК!</h2>
      <p>
        Lorem ipsum dolor sit amet consectetur adipisicing elit. Impedit
        temporibus, sint doloremque aspernatur quisquam error amet. Modi
        expedita, quas reiciendis quia nesciunt eaque aliquid, velit debitis id
        quibusdam explicabo vitae.
      </p>
      <h3>ЭТО H3 ЗАГОЛОВОК!</h3>
      <ul>
        <li>
          Lorem, ipsum dolor sit amet consectetur adipisicing elit. Illo culpa
          architecto amet rem accusantium nobis iure eum obcaecati voluptatem
          maiores, nam officiis itaque necessitatibus numquam. Quo magnam
          tenetur ad pariatur!
        </li>
        <li>
          Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim officia
          adipisci laborum sed, quae nobis doloribus, accusantium impedit, vel
          quos exercitationem ipsum cumque repellendus! Deleniti id eum sint
          porro dolores?
        </li>
        <li>
          Lorem ipsum dolor sit amet consectetur adipisicing elit. Nulla
          perferendis doloribus, repellat nesciunt quo voluptates inventore
          itaque provident atque iure ab mollitia praesentium officiis sint ea
          iste accusamus voluptatibus eligendi?
        </li>
      </ul>
      <div class="text-area__grid">
        <!-- item -->
        <div class="text-area__grid-item text-area">
          <div class="image-area">
			 <img src="/images/text-area-grid-1.jpg" alt="text-area-grid" />
          </div>
          <h4>ЭТО H4 ЗАГОЛОВОК!</h4>
          <p>
            Lorem ipsum dolor sit amet consectetur, adipisicing elit. Eaque
            excepturi et sunt molestias unde incidunt vel ipsa veniam nesciunt!
            Blanditiis autem quos enim? Accusamus eligendi repudiandae culpa
            incidunt enim laudantium.
          </p>
          <ul class="spoiler">
            <li class="spoiler-li">
              <div class="spoiler-li-title">
                Длинное название спойлера <i class="fa-solid fa-angle-down"></i>
              </div>
              <div class="spoiler-li-text">
                Существует правило: обновлений данных должно быть значительно
                меньше, чем чтения для их отдачи. То есть не имеет смысл
                агрегировать то, что изменится в тот же момент, при этом важна
                актуальность агрегированных данных. Что выбирать для
                агрегирования? Обычно это какая-то статистическая информация о
                числе записей, дате последнего обновления, авторе последнего
                обновления и тому подобное.
              </div>
            </li>
          </ul>
        </div>
        <!-- item -->
        <div class="text-area__grid-item text-area">
          <div class="image-area">
				 <img src="/images/text-area-grid-1.jpg" alt="text-area-grid" />
          </div>
          <h4>ЭТО H4 ЗАГОЛОВОК!</h4>
          <p>
            Lorem ipsum dolor sit amet consectetur, adipisicing elit. Eaque
            excepturi et sunt molestias unde incidunt vel ipsa veniam nesciunt!
            Blanditiis autem quos enim? Accusamus eligendi repudiandae culpa
            incidunt enim laudantium.
          </p>
          <p>
            Lorem ipsum dolor sit amet consectetur, adipisicing elit. Eaque
            excepturi et sunt molestias unde incidunt vel ipsa veniam nesciunt!
            Blanditiis autem quos enim? Accusamus eligendi repudiandae culpa
            incidunt enim laudantium.
          </p>
        </div>
      </div>
      <h4>ЭТО H4 ЗАГОЛОВОК!</h4>
      <p>
        Lorem ipsum dolor sit amet consectetur, adipisicing elit. Eaque
        excepturi et sunt molestias unde incidunt vel ipsa veniam nesciunt!
        Blanditiis autem quos enim? Accusamus eligendi repudiandae culpa
        incidunt enim laudantium.
      </p>
      <div class="table-bg">
        <table>
          <thead>
            <tr>
              <td>Заголовок 1</td>
              <td>Заголовок 2</td>
              <td>Заголовок 3</td>
              <td>Заголовок 4</td>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Значение 1</td>
              <td>Значение 2</td>
              <td>Значение 3</td>
              <td>Значение 4</td>
            </tr>
            <tr>
              <td>Значение 1</td>
              <td>Значение 2</td>
              <td>Значение 3</td>
              <td>Значение 4</td>
            </tr>
            <tr>
              <td>Значение 1</td>
              <td>Значение 2</td>
              <td>Значение 3</td>
              <td>Значение 4</td>
            </tr>
            <tr>
              <td>Значение 1</td>
              <td>Значение 2</td>
              <td>Значение 3</td>
              <td>Значение 4</td>
            </tr>
          </tbody>
        </table>
      </div>
      <p>
        Lorem ipsum dolor sit amet consectetur, adipisicing elit. Eaque
        excepturi et sunt molestias unde incidunt vel ipsa veniam nesciunt!
        Blanditiis autem quos enim? Accusamus eligendi repudiandae culpa
        incidunt enim laudantium.
      </p>
      <hr />
      <ul class="spoiler">
        <li class="spoiler-li">
          <div class="spoiler-li-title">
            Длинное название спойлера <i class="fa-solid fa-angle-down"></i>
          </div>
          <div class="spoiler-li-text">
            Существует правило: обновлений данных должно быть значительно
            меньше, чем чтения для их отдачи. То есть не имеет смысл
            агрегировать то, что изменится в тот же момент, при этом важна
            актуальность агрегированных данных. Что выбирать для агрегирования?
            Обычно это какая-то статистическая информация о числе записей, дате
            последнего обновления, авторе последнего обновления и тому подобное.
          </div>
        </li>
      </ul>
      <ul class="spoiler">
        <li class="spoiler-li">
          <div class="spoiler-li-title">
            Длинное название спойлера <i class="fa-solid fa-angle-down"></i>
          </div>
          <div class="spoiler-li-text">
            Существует правило: обновлений данных должно быть значительно
            меньше, чем чтения для их отдачи. То есть не имеет смысл
            агрегировать то, что изменится в тот же момент, при этом важна
            актуальность агрегированных данных. Что выбирать для агрегирования?
            Обычно это какая-то статистическая информация о числе записей, дате
            последнего обновления, авторе последнего обновления и тому подобное.
          </div>
        </li>
      </ul>
      <ul class="spoiler">
        <li class="spoiler-li">
          <div class="spoiler-li-title">
            Длинное название спойлера <i class="fa-solid fa-angle-down"></i>
          </div>
          <div class="spoiler-li-text">
            Существует правило: обновлений данных должно быть значительно
            меньше, чем чтения для их отдачи. То есть не имеет смысл
            агрегировать то, что изменится в тот же момент, при этом важна
            актуальность агрегированных данных. Что выбирать для агрегирования?
            Обычно это какая-то статистическая информация о числе записей, дате
            последнего обновления, авторе последнего обновления и тому подобное.
          </div>
        </li>
      </ul>
		<hr />
      <p>
        Lorem ipsum dolor sit amet consectetur, adipisicing elit. Eaque
        excepturi et sunt molestias unde incidunt vel ipsa veniam nesciunt!
        Blanditiis autem quos enim? Accusamus eligendi repudiandae culpa
        incidunt enim laudantium.
      </p>
      <p>
        Lorem ipsum dolor sit amet consectetur, adipisicing elit. Eaque
        excepturi et sunt molestias unde incidunt vel ipsa veniam nesciunt!
        Blanditiis autem quos enim? Accusamus eligendi repudiandae culpa
        incidunt enim laudantium.
      </p>
    </div>
  </div>
</div>






<script src="/js/lightbox.js"></script>
@endsection
