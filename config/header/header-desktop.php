<header class="pc-header ">
    <div class="header-wrapper">
        <div class="mr-auto pc-mob-drp">
            <ul class="list-unstyled">
                <li class="dropdown pc-h-item">
                    <h2>Update Cost</h2>
                </li>
            </ul>
        </div>
        <div class="ml-auto">
            <ul class="list-unstyled">
                <li class="dropdown pc-h-item">
                    <a class="pc-head-link dropdown-toggle arrow-none mr-0" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <img src="<?=home_url()?>assets/images/user/avatar-6.png" alt="user-image" class="user-avtar">
                        <span>
                            <span class="user-name"><?=user_info()?></span>
                            <span class="user-desc"><?=$current_user['user_role']?></span>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right pc-h-dropdown">
                        <a href="<?=home_url()?>page/user-account/change-password.php?ID=<?=base64_encode(json_encode($current_user['user_id']))?>" class="dropdown-item">
                            <i data-feather="lock"></i>
                            <span>เปลี่ยนรหัสผ่าน</span>
                        </a>
                        <a href="javascript:void(0)" class="dropdown-item" id="dropdown_user_logout">
                            <i data-feather="log-out"></i>
                            <span>ออกจากระบบ</span>
                        </a>
                        <input type="hidden" id="logout_url" value="<?=home_url()?>logout.php" readonly>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</header>