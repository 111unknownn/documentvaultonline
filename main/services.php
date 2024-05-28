<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../services.css">
    <link rel="icon" type="image/png" href="../images/favicons.png">
    <title>Services</title>

</head>
<style>
/* Add this CSS to your existing stylesheet */

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
         <li><a class="active" href="index">Home</a></li>
         <li><a class="active" href="../main/about">About</a></li>
         <li><a class="active" href="../main/services">Services</a></li>
      </ul>
   </nav>
    <div class="container">
        <h1>Our Services</h1>
        <div class="row">
            <div class="service">
                <i class="fa-solid fa-magnifying-glass"></i>
                <h2>Search</h2>
                <p>Lorem ipsum dolor sit amet consectet</p>
            </div>
            <div class="service">
                <i class="fa-solid fa-code-compare"></i>
                <h2>Version Control</h2>
                <p>Lorem ipsum dolor sit amet consectet</p>
            </div>
            <div class="service">
                <i class="fa-solid fa-down-left-and-up-right-to-center"></i>
                <h2>Collaboration</h2>
                <p>Lorem ipsum dolor sit amet consectet</p>
            </div>
            <div class="service">
                <i class="fa-regular fa-shield"></i>
                <h2>Security</h2>
                <p>Lorem ipsum dolor sit amet consectet</p>
            </div>
            <div class="service">
                <i class="fa-sharp fa-solid fa-universal-access"></i>
                <h2>Access Control</h2>
                <p>Lorem ipsum dolor sit amet consectet</p>
            </div>
            <div class="service">
                <i class="fa-regular fa-rectangle-list"></i>
                <h2>Compliance</h2>
                <p>Lorem ipsum dolor sit amet consectet</p>
            </div>
            <div class="service">
                <i class="fa-brands fa-creative-commons-nd"></i>
                <h2>WorkFlows</h2>
                <p>Lorem ipsum dolor sit amet consectet</p>
            </div>
            <div class="service">
                <i class="fa-sharp fa-regular fa-file"></i>
                <h2>File Management</h2>
                <p>Lorem ipsum dolor sit amet consectet</p>
            </div>
        </div>
    </div>
</body>


</html>
