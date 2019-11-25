<ul class="navigation">
    <?php
        if(isset($_GET['client_id'])){
            $client_id = $_GET['client_id'];
        ?>
        <li class="nav-item"><h6>Client Related</h6>
            <ul>
            <li><a href="sales_dashboard.php?client_id=<?php echo $client_id; ?>" title="Sales Dashboard"><i class="fas fa-chart-bar" style="padding-right: 10px;"></i>Sales Dashboard</a></li>
            <li><a href="catalog.php?client_id=<?php echo $client_id; ?>" title="View Client Catalog"><i class="fas fa-book-open" style="padding-right: 10px;"></i>Product Catalog</a></li>
            <li><a href="add_product.php?client_id=<?php echo $client_id; ?>"  title="Add New PDS"><i class="fas fa-plus-circle" style="padding-right: 10px;"></i>Add New PDS</a></li>
            <li><a href="view_marketing.php?client_id=<?php echo $client_id; ?>"  title="Add New E-marketing Form"><i class="fas fa-calendar-plus" style="padding-right: 10px;"></i>View E-Marketing</a></li>
            <li><a href="inventory.php?client_id=<?php echo $client_id; ?>" title="Inventory Admin"><i class="fas fa-box-open" style="padding-right: 10px;"></i>Inventory Admin</a></li>
            <li><a href="edit_client.php?client_id=<?php echo $client_id; ?>" title="Edit Client Info"><i class="fas fa-edit" style="padding-right: 10px;"></i>Edit Client</a></li>
                </ul>
        </li>
        <?php
        }
    ?>
    <li class="nav-item"><h6>Production</h6>
            <ul>
                <li><a href="embroidery_design_list.php" style="color: #fff;">Embroidery Design List</a></li>
                <?php
                    if($username == "dustingunter"){
                ?>
                <li><a href="online_order_schedule.php" style="color: #fff;">Online Order Schedule</a></li>
                <?php
                    } ?>
            </ul>
    </li>
    <?php
        if($username == "dustingunter" || $username == "Jennifer" || $username = "Jerica" || $username == "Amy" || $username = "Mary"){
    ?>
    <li class="nav-item"><h6>Sales</h6>
            <ul>
                <li><a href="sales_home.php" style="color: #fff;">Sales Home</a></li>
            </ul>
    </li>
    <?php
        } ?>
    <!--<li class="nav-item logout"><a href="logout.php">Logout</a></li> -->
</ul>