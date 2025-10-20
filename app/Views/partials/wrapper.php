<div class="app-wrapper">
      <!--begin::Header-->
      <?= $this->include('partials/header') ?>
      <!--end::Header-->

      <!--begin::Sidebar-->
      <?= $this->include('partials/sidebar') ?>
      <!--end::Sidebar-->

      <!--begin::App Main-->
      <main class="app-main">
        <!--begin::App Content Header-->
        <div class="app-content-header">
          <!--begin::Container-->
          <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
              <div class="col-sm-6">
                <!-- <h3 class="mb-0">Dashboard</h3> -->
                <h3 class="mb-0"><?= $title ?? 'Dashboard' ?></h3>
              </div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><a href="#">Home</a></li>
                  <li class="breadcrumb-item active" aria-current="page"><?= $title ?? 'Dashboard' ?></li>
                </ol>
              </div>
            </div>
            <!--end::Row-->
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content Header-->
        <!--begin::App Content-->
        <div class="app-content">
          <!--begin::Container-->
          <div class="container-fluid">
            <?= $this->renderSection('content') ?>
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content-->
      </main>
      <!--end::App Main-->
      
      <!--begin::Footer-->
      <?= $this->include('partials/footer') ?>
      <!--end::Footer-->
    </div>