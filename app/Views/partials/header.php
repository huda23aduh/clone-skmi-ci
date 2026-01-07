 <?php
//  var_dump(session()->get('user'));
//  die();
//  if (! session()->has('user')) {
//   die('AuthFilter triggered — no session found!');
// }

 ?>
 <!--begin::Header-->
 <nav class="app-header navbar navbar-expand bg-body fixed-top">
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
            <!--begin::language-->
            <div class="card-body">
                <form action="<?= base_url('profile/update-language') ?>" method="post" id="languageForm">
                    <?= csrf_field() ?>
                    <div class="mb-0">
                        <select name="language" class="form-select" onchange="document.getElementById('languageForm').submit()">
                            <?php foreach ($languages as $value => $label): ?>
                                <?php
                                // Get language directly from session, not from $user variable
                                // Priority: 1. user session language, 2. general session language, 3. default
                                $currentLanguage = 'english'; // default
                                
                                // Check user session first
                                $userSession = session()->get('user');
                                if (isset($userSession['language'])) {
                                    $currentLanguage = $userSession['language'];
                                }
                                // Fallback to general session
                                elseif (session()->has('language')) {
                                    $currentLanguage = session('language');
                                }
                                ?>
                                <option value="<?= $value ?>" <?= $currentLanguage === $value ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>
            <!--end::language-->
            <!--begin::User Menu Dropdown-->
            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <?php 
                    // Get user data DIRECTLY from session
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
                            <?= esc($userSession['name'] ?? 'Guest') ?>
                            <small>Member since Nov. 2023</small>
                        </p>
                    </li>
                    <!--end::User Image-->
                    <!--begin::Menu Footer-->
                    <li class="user-footer">
                        <a href="#" class="btn btn-default btn-flat">Profile</a>
                        <a href="<?= base_url('/logout') ?>" class="btn btn-default btn-flat float-end">Sign out</a>
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