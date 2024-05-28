const readMoreBtn = document.querySelector('.read-more');
const additionalSections = document.querySelector('.additional-sections');

let isSectionsVisible = false;

readMoreBtn.addEventListener('click', function (event) {
   event.preventDefault();
   if (!isSectionsVisible) {
      additionalSections.style.display = 'block';
      isSectionsVisible = true;
   } else {
      additionalSections.style.display = 'none';
      isSectionsVisible = false;
   }
});