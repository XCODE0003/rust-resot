// Show stats

$(function () {
    $('.check-stats').on('click', function() {
      $(".stats-info").toggleClass('active');
      $(".check-stats").toggleClass('active');
    });
});



// Select 

const select = document.querySelectorAll('.select-type');
const option = document.querySelectorAll('.option');
let index = 1;

select.forEach(a => {
	a.addEventListener('click', b => {
		const next = b.target.nextElementSibling;
		next.classList.toggle('toggle');
		next.style.zIndex = index++;
	})
})
option.forEach(a => {
	a.addEventListener('click', b => {
		b.target.parentElement.classList.remove('toggle');
		
		const parent = b.target.closest('.select').children[0];
		parent.setAttribute('data-type', b.target.getAttribute('data-type'));
		parent.innerText = b.target.innerText;
	})
})


$(function () {
  $('.select-stats').on('click', function() {
    $(this).toggleClass('active');
  });
});