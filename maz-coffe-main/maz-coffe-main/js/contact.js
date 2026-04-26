// ===== FONCTIONS POUR LA PAGE CONTACT =====
function initContactPage() {
    const contactForm = document.getElementById('contact-form');
    if (!contactForm) return;

    contactForm.addEventListener('submit', function (e) {
        e.preventDefault();

        // Réinitialiser les messages d'erreur
        document.querySelectorAll('.error-message').forEach(msg => {
            msg.style.display = 'none';
        });

        // Valider le formulaire
        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const message = document.getElementById('message').value.trim();

        let isValid = true;

        // Validation du nom
        if (name === '') {
            document.getElementById('name-error').style.display = 'block';
            isValid = false;
        }

        // Validation de l'email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            document.getElementById('email-error').style.display = 'block';
            isValid = false;
        }

        // Validation du message
        if (message === '') {
            document.getElementById('message-error').style.display = 'block';
            isValid = false;
        }

        // Si le formulaire est valide
        if (isValid) {
            // Envoi réel des données au serveur
            const submitBtn = contactForm.querySelector('.submit-btn');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Envoi en cours...';
            submitBtn.disabled = true;

            const formData = new FormData(contactForm);

            fetch('save_message.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Afficher le message de succès
                    const successMessage = document.getElementById('success-message');
                    if (successMessage) {
                        successMessage.textContent = data.message;
                        successMessage.style.display = 'block';
                    }

                    // Réinitialiser le formulaire
                    contactForm.reset();

                    // Masquer le message de succès après 5 secondes
                    setTimeout(() => {
                        if (successMessage) {
                            successMessage.style.display = 'none';
                        }
                    }, 5000);
                } else {
                    alert(data.message || 'Une erreur est survenue.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Une erreur est survenue lors de l\'envoie du message.');
            })
            .finally(() => {
                // Réactiver le bouton
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        }
    });
}

// Initialiser la page contact au chargement
document.addEventListener('DOMContentLoaded', initContactPage);
