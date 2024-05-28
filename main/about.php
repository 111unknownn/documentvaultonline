<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <link rel="stylesheet" href="../css/about_us.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="icon" type="image/png" href="../images/favicons.png">
   <title>About Us</title>
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Add this line -->
</head>

<style>
   /* Hide the hamburger icon when the screen width is not small */
@media screen and (min-width: 858px) {
    .checkbtn {
        display: none;
    }
}

</style>

<body>
   <nav class="navbar" id="navbar">
      <input type="checkbox" id="check">
      <label for="check" class="checkbtn">
         <i class="fas fa-bars"></i>
      </label>
   <a href="index"><label class="logo" style="cursor:pointer;">DocuVault</label></a>
      <ul>
         <li><a class="active" href="index.php">Home</a></li>
         <li><a class="active" href="about.php">About</a></li>
         <li><a class="active" href="services.php">Services</a></li>

      </ul>
   </nav>
   <div class="heading">
      <h1>About Us</h1>
      <p>Beyond Paper: Your Documents, Our Expertise – Seamlessly Managed.
Streamline, Secure, Succeed: Elevate Your Document Experience with Our Management System.</p>
   </div>
   <!-- ... (previous code) ... -->
   <div class="container">
      <section class="about">
         <div class="about-image">
            <img src="../images/dms.jpg" alt="">
         </div>
         <div class="about-content">
            <h2>Document Management System enhances paperless work.</h2>
            <p>A docu-vault is a software solution that helps organizations store, manage, track, and retrieve documents
               and files in a secure and organized manner. It provides a centralized repository for documents, allowing
               users to create, edit, collaborate, and share files with ease. Docu-vault typically includes features
               such as version control, document indexing, search capabilities, access control, workflow automation, and
               integration with other business systems. It helps streamline document-centric processes, improve
               productivity, enhance collaboration, ensure regulatory compliance, and reduce paper usage.</p>

            <a href="#" class="read-more">Read More</a>
            <div class="additional-sections" style="display: none;">
               <div class="additional-section">
                  <div class="additional-heading">
                     <h3>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Aperiam quam deserunt qui odit
                           expedita incidunt sit totam dolorem ut, ab, optio quos. Amet totam doloribus unde nam
                           eligendi est dicta! Lorem ipsum dolor, sit amet consectetur adipisicing elit. Ullam libero
                           animi mollitia quaerat! Quam omnis perspiciatis, repellendus doloremque minus rem, recusandae
                           optio voluptatibus necessitatibus veniam amet quos sed laborum reiciendis. Lorem ipsum dolor
                           sit, amet consectetur adipisicing elit. Est recusandae voluptate porro velit saepe harum in
                           atque numquam tempora fugit, veniam ipsum magni eaque sapiente ex ratione quis, nesciunt cum.
                           Lorem ipsum dolor sit amet consectetur adipisicing elit. Molestiae animi optio aut tenetur
                           assumenda ipsa recusandae quo excepturi ex officiis saepe esse deserunt ullam, inventore
                           incidunt atque ut. Dolorem, expedita? Lorem ipsum, dolor sit amet consectetur adipisicing
                           elit. Harum blanditiis minima, fugit hic deserunt consectetur nulla voluptas quae facere
                           eligendi vero porro neque inventore dolore obcaecati consequuntur labore vel a.</p>
                     </h3>
                  </div>
                  <div class="additional-content">
                     <p>This is an additional paragraph for the new section.</p>
                  </div>
               </div>
               <div class="additional-section">
                  <div class="additional-heading">
                     <h3>
                        <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Tempora fugit rerum nostrum quas
                           doloribus, corrupti reiciendis sapiente doloremque veniam animi. Sunt modi ipsam ullam culpa
                           dolore sequi iure dicta nihil.</p>
                     </h3>
                  </div>
                  <div class="additional-content">
                     <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Dolores consequuntur sunt suscipit
                        beatae minus, earum magnam ad, nobis eaque in quo culpa similique expedita incidunt saepe dicta
                        iste. Possimus, soluta? Lorem ipsum, dolor sit amet consectetur adipisicing elit. Esse optio
                        maiores eius, sit blanditiis eligendi nulla eveniet velit sint maxime minus dignissimos
                        necessitatibus aperiam dolores aspernatur omnis magnam quisquam! Ipsum. Lorem ipsum dolor sit,
                        amet consectetur adipisicing elit. Unde consequatur laboriosam, cumque deserunt veniam officiis
                        autem voluptatibus, mollitia blanditiis iste fugiat natus perferendis corrupti ea eum totam sint
                        illum? Optio?</p>
                  </div>
               </div>
            </div>
         </div>
      </section>
   </div>


   <footer class="footer" id="footer">
      <div class="footer-bottom">
         <p>&copy; 2023 DocuVault. All rights reserved.</p>
      </div>
   </footer>
  
   <!-- Add the jQuery library here -->
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   <script>
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
 
      var lastScrollTop = 0;

      $(window).scroll(function() {
          var st = $(this).scrollTop();
          var navbar = $('nav');

          if (st > lastScrollTop) {
              // scrolling down
              navbar.removeClass('scrolled');
          } else {
              // scrolling up
              navbar.addClass('scrolled');
          }
          lastScrollTop = st;
      });
   </script>
</body>
</body>

</html>