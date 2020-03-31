function ajustarNavbar() {
    var x = document.getElementsByClassName("topnav")[0];
    if (x.className === "topnav") {
      x.className += " responsive";
    } else {
      x.className = "topnav";
    }
  }