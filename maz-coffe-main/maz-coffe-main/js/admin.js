// admin.js - Handling Admin AJAX Operations

document.addEventListener('DOMContentLoaded', function() {
    initAdminAJAX();
});

function initAdminAJAX() {
    // 1. Handle Product Form Submissions (Add/Update)
    const productForm = document.querySelector('.drawer-form');
    if (productForm && productForm.querySelector('[name="add_product"], [name="update_product"]')) {
        productForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleProductSubmit(this);
        });
    }

    // 2. Custom Style Confirmation for Deletion
    document.addEventListener('click', function(e) {
        const deleteBtn = e.target.closest('.delete-btn');
        if (deleteBtn && deleteBtn.href && deleteBtn.href.includes('delete_id=')) {
            e.preventDefault();
            const deleteUrl = deleteBtn.href;
            
            // Create modal if it doesn't exist
            let overlay = document.querySelector('.custom-confirm-overlay');
            if (!overlay) {
                overlay = document.createElement('div');
                overlay.className = 'custom-confirm-overlay';
                overlay.innerHTML = `
                    <div class="custom-confirm-modal">
                        <div class="custom-confirm-icon"><i class="fas fa-trash"></i></div>
                        <h3 class="custom-confirm-title">Suppression</h3>
                        <p class="custom-confirm-text">Voulez-vous vraiment supprimer cet élément ? Cette action est irréversible.</p>
                        <div class="custom-confirm-actions">
                            <button class="btn-confirm-cancel">Annuler</button>
                            <button class="btn-confirm-delete">Oui, supprimer</button>
                        </div>
                    </div>
                `;
                document.body.appendChild(overlay);
                
                overlay.querySelector('.btn-confirm-cancel').addEventListener('click', () => {
                    overlay.classList.remove('active');
                });
            }
            
            // Set action for the delete button
            const confirmBtn = overlay.querySelector('.btn-confirm-delete');
            const newConfirmBtn = confirmBtn.cloneNode(true);
            confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
            
            newConfirmBtn.addEventListener('click', () => {
                newConfirmBtn.innerText = 'Suppression...';
                newConfirmBtn.style.opacity = '0.7';
                newConfirmBtn.style.pointerEvents = 'none';
                window.location.href = deleteUrl;
            });
            
            // Show modal
            setTimeout(() => overlay.classList.add('active'), 10);
        }
    });
}

/**
 * Handles Add/Update Product via AJAX
 */
async function handleProductSubmit(form) {
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerText;
    
    // Add logic to determine if it's add or update based on the button name
    const actionName = submitBtn.name; // add_product or update_product
    formData.append(actionName, '1');

    submitBtn.innerText = 'Traitement...';
    submitBtn.disabled = true;

    try {
        const response = await fetch('admin.php', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();
        
        if (data.status === 'success') {
            showNotification(data.message);
            
            // Close drawer
            if (typeof closeDrawer === 'function') closeDrawer();
            
            // Update Stat Counter
            if (data.new_count !== undefined) {
                const counter = document.getElementById('stat-products');
                if (counter) counter.innerText = data.new_count;
            }

            // Reload products in the table (or we could prepend/update the row)
            // For simplicity and correctness (to handle sorting/etc), let's just reload the table body
            // or redirect if it's a major change. But user wants NO reload.
            // Let's reload just the table part or the whole page content via fetch.
            refreshProductTable();
            
        } else {
            alert(data.message || 'Une erreur est survenue.');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Erreur lors de la communication avec le serveur.');
    } finally {
        submitBtn.innerText = originalText;
        submitBtn.disabled = false;
    }
}

/**
 * Handles Deletion of Products or Messages via AJAX
 */
async function handleDeleteAction(btn) {
    const url = btn.href;
    const isMessage = url.includes('admin_messages.php');
    const row = btn.closest('tr');

    try {
        const response = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();

        if (data.status === 'success') {
            showNotification(data.message);
            
            // Remove row from DOM with a fade out
            if (row) {
                row.style.transition = 'opacity 0.3s, transform 0.3s';
                row.style.opacity = '0';
                row.style.transform = 'translateX(20px)';
                setTimeout(() => row.remove(), 300);
            }

            // Update respective Stat Counter
            const counterId = isMessage ? 'stat-messages' : 'stat-products';
            const counter = document.getElementById(counterId);
            if (counter && data.new_count !== undefined) {
                counter.innerText = data.new_count;
            }
        } else {
            alert(data.message || 'Erreur lors de la suppression.');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Erreur réseau lors de la suppression.');
    }
}

/**
 * Refreshes the product table content without a full page reload
 */
async function refreshProductTable() {
    try {
        const response = await fetch('admin.php');
        const html = await response.text();
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        // Update the table body
        const newTable = doc.querySelector('.premium-table tbody');
        const currentTable = document.querySelector('.premium-table tbody');
        if (newTable && currentTable) {
            currentTable.innerHTML = newTable.innerHTML;
        }
    } catch (error) {
        console.error('Error refreshing table:', error);
    }
}

