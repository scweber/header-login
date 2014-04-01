/*
 * Code to hide/show the Role Dropdown based on Create New Users selection
 */
function toggleNewUserRole(create, blogID) {
    if(create === '0') {
        document.getElementById('new-user-role'+blogID).style.display='none';
    } else {
        document.getElementById('new-user-role'+blogID).style.display='block';
    }
}