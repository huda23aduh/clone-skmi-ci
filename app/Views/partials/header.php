 <?php
//  var_dump(session()->get('user'));
//  die();
//  if (! session()->has('user')) {
//   die('AuthFilter triggered — no session found!');
// }

 ?>
 <!--begin::Header-->
 <nav class="app-header navbar navbar-expand bg-body">
        <!--begin::Container-->
        <div class="container-fluid">
          <!--begin::Start Navbar Links-->
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                <i class="bi bi-list"></i>
              </a>
            </li>
          </ul>
          <!--end::Start Navbar Links-->
          <!--begin::End Navbar Links-->
          <ul class="navbar-nav ms-auto">
            <!--begin::Navbar Search-->
            <li class="nav-item">
              <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                <i class="bi bi-search"></i>
              </a>
            </li>
            <!--end::Navbar Search-->
            <!--begin::Fullscreen Toggle-->
            <li class="nav-item">
              <a class="nav-link" href="#" data-lte-toggle="fullscreen">
                <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
                <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none"></i>
              </a>
            </li>
            <!--end::Fullscreen Toggle-->
            <!--begin::User Menu Dropdown-->
            <li class="nav-item dropdown user-menu">
              <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                <?php 
                // Get user data from session safely
                $userSession = session()->get('user') ?? [];
                $profileImage = $userSession['profile_image'] ?? '';
                $userName = $userSession['name'] ?? 'Guest';
                
                // Check if profile image exists and is accessible
                $imageExists = !empty($profileImage) && file_exists(FCPATH . $profileImage);
                ?>
                
                <?php if ($imageExists): ?>
                    <img
                        src="<?= base_url($profileImage) ?>" 
                        class="user-image rounded-circle shadow"
                        alt="User Image"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                    />
                    <div class="user-image-placeholder rounded-circle bg-secondary d-none" style="width: 32px; height: 32px;">
                        <i class="fas fa-user text-white small"></i>
                    </div>
                <?php else: ?>
                    <div class="user-image-placeholder rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="fas fa-user text-white small"></i>
                    </div>
                <?php endif; ?>
                
                <span class="d-none d-md-inline"><?= esc($userName) ?></span>
            </a>
              <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                <!--begin::User Image-->
                <li class="user-header text-bg-primary">
                  <img
                    src="./assets/img/user2-160x160.jpg"
                    class="rounded-circle shadow"
                    alt="User Image"
                  />
                  <p>
                  <?= esc(session()->get('user')['name'] ?? 'Guest') ?>
                  <!-- Alexander Pierce - Web Developer -->
                    <small>Member since Nov. 2023</small>
                  </p>
                </li>
                <!--end::User Image-->
                <!--begin::Menu Body-->
                <li class="user-body">
                  <!--begin::Row-->
                  <!-- <div class="row">
                    <div class="col-4 text-center"><a href="#">Followers</a></div>
                    <div class="col-4 text-center"><a href="#">Sales</a></div>
                    <div class="col-4 text-center"><a href="#">Friends</a></div>
                  </div> -->
                  <!--end::Row-->
                </li>
                <!--end::Menu Body-->
                <!--begin::Menu Footer-->
                <li class="user-footer">
                  <a href="#" class="btn btn-default btn-flat">Profile</a>
                  <a  href="<?= base_url('/logout') ?>" class="btn btn-default btn-flat float-end">Sign out</a>
                </li>
                <!--end::Menu Footer-->
              </ul>
            </li>
            <!--end::User Menu Dropdown-->
          </ul>
          <!--end::End Navbar Links-->
        </div>
        <!--end::Container-->
      </nav>
      <!--end::Header-->