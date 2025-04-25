    // Fonction pour afficher les messages dans la boîte de chat
    function appendMessage(message, sender) {
        const chatContainer = document.getElementById('chat-container');
        const messageDiv = document.createElement('div');
        messageDiv.classList.add('message');
        messageDiv.classList.add(sender);
        messageDiv.textContent = message;
        chatContainer.appendChild(messageDiv);
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }

    // Fonction pour envoyer un message à l'API
    async function sendMessage() {
        const userInput = document.getElementById('user-input').value;
        if (!userInput) return;
        
        // Afficher le message utilisateur
        appendMessage(userInput, 'user');
        
        // Vider l'input
        document.getElementById('user-input').value = '';
        
        // Appeler l'API PHP pour obtenir la réponse du chatbot
        try {
            const response = await fetch('http://localhost:58036/index.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message: userInput })
            });
            
            const data = await response.text();
            appendMessage(data, 'chatbot');  // Afficher la réponse du chatbot
        } catch (error) {
            console.error('Erreur de communication avec le serveur:', error);
            appendMessage("Désolé, il y a eu une erreur. Veuillez réessayer.", 'chatbot');
        }
    }
