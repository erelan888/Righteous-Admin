<div class="header-nav-identity">
    <span><?php echo $title; ?></span><div class="dropdown show" style="display: inline;">
  <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <i class="fas fa-user" style="padding-right: 10px; padding-left: 10px;"></i><?php echo $username; ?>
  </a>

  <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
    <a class="dropdown-item" href="https://admin.authenticmerch.com/change_password.php">Change Password</a>
    <a class="dropdown-item" href="https://admin.authenticmerch.com/add_product.php">Add New PDS</a>
    <?php if($permission_level == 0){ ?>
        <a class="dropdown-item" href="https://app.powerbi.com/home">Power BI</a>
    <?php } ?>
    <a class="dropdown-item" href="https://admin.authenticmerch.com/logout.php">Logout</a>
  </div>
</div>
    <?php
        $notification_query = "SELECT COUNT(*) as notifications FROM `admin_user_notifications` WHERE `destination_user`=" . $user_id;
        $results = mysqli_query($conn, $notification_query);
        $notifications = mysqli_fetch_assoc($results);
    ?>
    
    <i class="far fa-bell" id="notification-bell" style="font-size: 20px; padding-left: 10px; cursor: pointer;"></i><span class="badge badge-danger"><?php echo $notifications['notifications']; ?></span>
</div> 
        <script type="text/javascript">
            $(document).ready(function(){
                var notifications = <?php echo $notifications['notifications']; ?>;
                $("#notification-bell").click(function(){
                    if(notifications != 0){
                        window.location.href = "https://admin.authenticmerch.com/notification_manager.php";
                    }
                });
                
            });
            
            
        </script>
<div class="quick-nav">
    Quick nav: <select class="form-control-sm" name="quick-nav-client" id="quick-nav-client" style="margin-right: 10px;"><option name="Choose Client" value="0">Choose Client</option>
     <?php 
            $client_query = "SELECT * FROM `admin_client_details` ORDER BY client_name ASC";
            $results = mysqli_query($conn, $client_query);
            while($client = mysqli_fetch_assoc($results)){
                echo "<option name='" . $client['client_name'] . "' value='" . $client['_client_id'] . "'>" . $client['client_name'] . "</option>"; 
            }
     ?>
     </select><select class="form-control-sm" id="quick-nav-page"><option name="Choose Page" value="">Choose Page</option>
     <option name="Sales Dashboard" value="sales_dashboard">Sales Dashboard</option>
     <option name="Product Catalog" value="catalog">Product Catalog</option>
     <option name="Inventory Admin" value="inventory">Inventory Admin</option></select><a id="quick-nav-go" class="btn btn-secondary btn-sm" style="margin-left: 5px; position: relative; top: -2px;"><i class="fas fa-arrow-right" style="color: #fff; cursor: pointer;"></i></a>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $("#quick-nav-go").click(function(){
            var client = $("#quick-nav-client").text();
            var clientId = $("#quick-nav-client").val();
            var page = $("#quick-nav-page").text();
            var pageLocation = $("#quick-nav-page").val();

            if(clientId != 0 && pageLocation != ""){
                var url = "https://admin.authenticmerch.com/" + pageLocation + ".php?client_id=" + clientId;

                window.location.href = url;
            }
        });
    });
</script>