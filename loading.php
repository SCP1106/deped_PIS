<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>DepEd Nueva Ecija</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet" />
    <link rel="stylesheet" href="css/loading.css" />
  </head>
  <body>
    <div class="blob" id="blob1"></div>
    <div class="blob" id="blob2"></div>
    <div class="blob" id="blob3"></div>

    <!-- Loading Screen -->
    <div
      class="loader-container position-fixed top-0 start-0 w-100 h-100 d-flex flex-column justify-content-center align-items-center">
      <div class="text-center">
        <div class="logo-wrapper mb-3">
          <div class="rotating-border"></div>
          <div class="rotating-border-inner"></div>
          <img
            src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/sdone-logo-R1twHOYRot4GEzkYSHCILNcCeKKz61.png"
            alt="Logo"
            class="logo-static" />
        </div>
        <div class="titles">
          <p class="mb-1 fw-bold">Republic of the Philippines</p>
          <p class="mb-1 fw-bold">Department of Education</p>
          <p class="mb-0 fw-bold">SCHOOLS DIVISION OFFICE OF NUEVA ECIJA</p>
        </div>
        <h4 class="text-success mt-4">Loading...</h4>
      </div>
    </div>

    <!-- Offline Message -->
    <div
      class="offline-message position-fixed top-0 start-0 w-100 p-3 text-center">
      <p class="m-0">
        You are currently offline. Please check your internet connection.
      </p>
    </div>

    <!-- Main Content -->
    <div class="content container-fluid">
      <div class="row min-vh-100 justify-content-center align-items-center">
        <div class="col-12 col-md-8 col-lg-6 text-center">
          <img
            src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/sdone-logo-R1twHOYRot4GEzkYSHCILNcCeKKz61.png"
            alt="Logo"
            class="main-logo mb-4" />
        </div>
      </div>
    </div>

    <script src="js/loading.js"></script>
  </body>
</html>
