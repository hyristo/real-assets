<?php
if (!$LoggedAccount->HasPermission(THIS_PERMISSION)){     
     Utils::RedirectTo(BASE_HTTP . 'not_permission.php?NOT_PERMISSION=1');
}
?>
<script type="text/javascript">

var permission = {
    Read: '<?=$LoggedAccount->HasPermission(THIS_PERMISSION, 'READ')?>',
    Update: '<?=$LoggedAccount->HasPermission(THIS_PERMISSION, 'UPDATE')?>',
    Delete: '<?=$LoggedAccount->HasPermission(THIS_PERMISSION, 'DELETE')?>'
};

</script>