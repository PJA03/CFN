<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Store original values
        const originalValues = {
            username: document.querySelector('input[name="username"]').value,
            email: document.querySelector('input[name="email"]').value,
            first_name: document.querySelector('input[name="first_name"]').value,
            last_name: document.querySelector('input[name="last_name"]').value,
            contact_no: document.querySelector('input[name="contact_no"]').value,
            address: document.querySelector('input[name="address"]').value
        };

        let changesMade = false;

        // Function to check for changes
        function checkForChanges() {
            const currentValues = {
                username: document.querySelector('input[name="username"]').value,
                email: document.querySelector('input[name="email"]').value,
                first_name: document.querySelector('input[name="first_name"]').value,
                last_name: document.querySelector('input[name="last_name"]').value,
                contact_no: document.querySelector('input[name="contact_no"]').value,
                address: document.querySelector('input[name="address"]').value
            };

            // Send AJAX request to check for changes
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'check_changes.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    changesMade = xhr.responseText === 'true';
                }
            };
            xhr.send('username=' + encodeURIComponent(currentValues.username) +
                     '&email=' + encodeURIComponent(currentValues.email) +
                     '&first_name=' + encodeURIComponent(currentValues.first_name) +
                     '&last_name=' + encodeURIComponent(currentValues.last_name) +
                     '&contact_no=' + encodeURIComponent(currentValues.contact_no) +
                     '&address=' + encodeURIComponent(currentValues.address));
        }

        // Add event listeners to input fields
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', checkForChanges);
        });

        document.getElementById('profile_image').addEventListener('change', function() {
            changesMade = true;
        });

        document.getElementById('profileForm').addEventListener('submit', function(event) {
            var email = document.querySelector('input[name="email"]').value;
            var contact_no = document.querySelector('input[name="contact_no"]').value;
            var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            var phonePattern = /^\d{11}$/;

            if (!emailPattern.test(email)) {
                event.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Email',
                    text: 'Please enter a valid email address.'
                });
                return;
            }

            if (!phonePattern.test(contact_no)) {
                event.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Phone Number',
                    text: 'Please enter a valid 11-digit phone number.'
                });
                return;
            }

            if (!changesMade) {
                event.preventDefault();
                Swal.fire({
                    icon: 'info',
                    title: 'No Changes',
                    text: 'No changes were made to your profile.'
                }).then(() => {
                window.location.href = 'UserProfile.php';
            });
        
            }
        });

        document.getElementById('cancel').addEventListener('click', function() {
            Swal.fire({
                title: 'Discard Changes?',
                text: "You will lose any unsaved changes",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1F4529',
                cancelButtonColor: '#D34646',
                confirmButtonText: 'Yes, cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'UserProfile.php';
                }
            });
        });

        // Show success alert if update was successful
        <?php if ($updateSuccess): ?>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Your profile has been updated successfully.'
            }).then(() => {
                window.location.href = 'UserProfile.php';
            });
        <?php endif; ?>
    });
</script>