<nav class="pc-sidebar">
	<div class="navbar-wrapper">
		<div class="m-header">
			<div class="b-brand">
				<!-- ========   change your logo hear   ============ -->
				<img src="<?=home_url()?>assets/images/logo-white.svg" alt="" class="logo logo-lg">
			</div>
		</div>
		<div class="navbar-content">
			<ul class="pc-navbar">
				<li class="pc-item pc-caption">
					<label>Data Cost</label>
				</li>
				<li class="pc-item">
					<a href="<?=home_url()?>page/dashboard.php" class="pc-link "><span class="pc-micon"><i class="ti-calendar"></i></span><span class="pc-mtext">Dashboard</span></a>
				</li>
				<li class="pc-item">
					<a href="<?=home_url()?>page/data-cost/list.php" class="pc-link "><span class="pc-micon"><i class="ti-calendar"></i></span><span class="pc-mtext">Data Cost</span></a>
				</li>
				<li class="pc-item">
					<a href="<?=home_url()?>page/upload-standardcost/standardcost.php" class="pc-link "><span class="pc-micon"><i class="ti-upload"></i></span><span class="pc-mtext">Update Standardcost</span></a>
				</li>
				<li class="pc-item">
					<a href="<?=home_url()?>page/upload-saleprice/saleprice.php" class="pc-link "><span class="pc-micon"><i class="ti-upload"></i></span><span class="pc-mtext">Update SalePrice</span></a>
				</li>
				<li class="pc-item">
					<a href="<?=home_url()?>page/upload-buyprice/buyprice.php" class="pc-link "><span class="pc-micon"><i class="ti-upload"></i></span><span class="pc-mtext">Update BuyPrice</span></a>
				</li>
				<li class="pc-item pc-caption">
					<label>Price List</label>
				</li>
				<li class="pc-item">
					<a href="<?=home_url()?>page/data-pricelist/list.php" class="pc-link "><span class="pc-micon"><i class="ti-calendar"></i></span><span class="pc-mtext">Data Price-list</span></a>
				</li>
				<li class="pc-item">
					<a href="<?=home_url()?>page/upload-pricelist/pricelist.php" class="pc-link "><span class="pc-micon"><i class="ti-upload"></i></span><span class="pc-mtext">Update Price-list</span></a>
				</li>
				<li class="pc-item">
					<a href="javascript:void(0)" class="pc-link " id="user_logout"><span class="pc-micon"><i data-feather="log-out"></i></span><span class="pc-mtext">ออกจากระบบ</span></a>
					<input type="hidden" id="logout_url" value="<?=home_url()?>logout.php" readonly>
				</li>
			</ul>
		</div>
	</div>
</nav>
