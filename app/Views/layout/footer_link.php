<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('public/assets/js/jquery-3.6.0.min.js') ?>" ></script>
<script src="<?= base_url('public/assets/js/bootstrap.bundle.min.js') ;?>"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.40.0/dist/apexcharts.min.js"></script>
<script src="<?= base_url('public/assets/js/toastr.min.js') ;?>"></script>
<script src="https://unpkg.com/alpinejs"></script>
<script src="<?=base_url('public/assets/js/app.js');?>" ></script>
<script src="<?=base_url('public/assets/js/script.js');?>" ></script>
<script src="<?=base_url('public/assets/js/notifications.js');?>" ></script>

    <script>
    App.init({
        'siteUrl' : '<?=base_url()?>'
    })
        // Toggle sidebar on mobile
        document.querySelector('.toggle-sidebar').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('show');
        });
        
        // Handle clicks outside sidebar to close it on mobile
        document.querySelector('.main-content').addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                document.querySelector('.sidebar').classList.remove('show');
            }
        });
        
        // Fetch notification count
        function fetchNotificationCount() {
            fetch('<?= base_url('notifications/count') ?>')
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('notification-badge');
                    const sidebarCount = document.getElementById('sidebar-notification-count');
                    
                    if (data.unread_count > 0) {
                        badge.textContent = data.unread_count;
                        badge.style.display = 'flex';
                        
                        sidebarCount.textContent = data.unread_count;
                        sidebarCount.style.display = 'inline-block';
                    } else {
                        badge.style.display = 'none';
                        sidebarCount.style.display = 'none';
                    }
                })
                .catch(error => console.error('Error fetching notification count:', error));
        }
        
           // Fetch notifications for dropdown
     function fetchNotifications() {
            fetch("<?= base_url('notifications/fetch') ?>")
                .then(response => response.json())
                .then(data => {
                    const itemsContainer = document.getElementById("notification-items");
                    const badge = document.getElementById("notification-badge");

                    // Clear and populate notifications
                    itemsContainer.innerHTML = '';

                    if (data.notifications.length === 0) {
                        itemsContainer.innerHTML = '<p class="text-center p-3 small">No new notifications</p>';
                    } else {
                        data.notifications.forEach(note => {
                            const item = `
                                <div class="dropdown-item" onclick="viewNotification('${note.id}')">
                                        <strong>${note.title}</strong><br>
                                        <small>${note.message}</small>
                                </div>
                            `;
                            itemsContainer.innerHTML += item;
                        });
                    }

                    // Update unread badge
                    badge.innerText = data.unread_count > 0 ? data.unread_count : '';
                })
                .catch(err => {
                    console.error("Error fetching notifications", err);
                    document.getElementById("notification-items").innerHTML = '<p class="text-danger text-center p-2 small">Failed to load notifications.</p>';
                });
        }
        
        // Load notifications when dropdown is opened
        document.getElementById('notificationDropdown').addEventListener('click', function() {
            fetchNotifications();
        });
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            fetchNotificationCount();
            
            // Refresh notification count every 30 seconds
            setInterval(fetchNotificationCount, 30000);
        });
    </script>