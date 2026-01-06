var swiper = new Swiper(".header-news", {
  spaceBetween: 30,
  slidesPerView: 2,
  watchSlidesVisibility: true,
  watchSlidesProgress: true,

  navigation: {
    nextEl: ".swiper-button-next",
    prevEl: ".swiper-button-prev",
  },

  pagination: {
    el: ".swiper-pagination",
    clickable: true,
  },

  breakpoints: {
    300: {
      slidesPerView: 1,
      spaceBetween: 15,
    },

    769: {
      slidesPerView: 1,
      spaceBetween: 40,
    },
  },
});

$(".tab-nav span").on("click", function () {
  $([$(this).parent()[0], $($(this).data("href"))[0]])
    .addClass("active")
    .siblings(".active")
    .removeClass("active");
});

// add class on click profile deposit popup

$(".styles__Method-sc-ena6u4-3").on("click", function () {
  $(".styles__Method-sc-ena6u4-3").removeClass("fdLgLR");
  $(this).addClass("fdLgLR");
});

$(".shop-item-buy").on("click", function () {
	$("html").addClass("overflow");
});

$(".sb-popup_back").on("click", function () {
	$("html").removeClass("overflow");
 });

$(".trading-link-copy__link--img").click(function () {
  $(".info__popup").css({
    display: "flex",
  });
});

$(".modal-close,.buying-item-close").click(function () {
  $(".balance-modal").removeClass("spbim-block");
});
$(".spc__close, .sb-popup_back").click(function () {
  $(".sb__popup").css({
    display: "none",
  });
   $("body").removeClass("hidden");
});
$(".spc-gift__text").click(function () {
  $(".spc__gift").toggleClass("active");
});

// spc end
$(".info-popup__close,.info-popup-content__close").click(function () {
  $(".info__popup").css({
    display: "none",
  });
});

$(function () {
  $(".spoiler-li-title").on("click", function () {
    $(this).closest(".spoiler-li").toggleClass("active");
  });
});
