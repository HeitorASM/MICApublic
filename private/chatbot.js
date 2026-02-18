//esse arquivo responsavel pelo funcioanmento total da logica do chatbot nem de longe e o ideal uasr o JS para 
//fazer isso sem suar nenhuma forma de proteção para a chave e eu sei disso o unico motivo ser feito assim foi 
//puramente por questoes de tempo, o ideal e usar node ou python aqui (e eu usaria se o projeto ja não tive-se sido ja entreque)
//desculpa italo kkkkkkkk


let chatHistory = [];
const API_KEY = "--------------------";//caht da API do grok aqui ou pelo .env

document.addEventListener('DOMContentLoaded', function() {
    initializeChatbot();
    updateMessageTime();
});

function initializeChatbot() {
    const userInput = document.getElementById('user-input');
    const sendBtn = document.getElementById('send-btn');
    const micBtn = document.getElementById('mic-btn');
    
    sendBtn.addEventListener('click', sendMessage);
    micBtn.addEventListener('click', toggleVoiceInput);
    
    userInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });
    
    addQuickQuestions();
}

async function sendMessage() {
    const userInput = document.getElementById('user-input');
    const message = userInput.value.trim();
    
    if (!message) return;
    
    // Adicionar mensagem do usuário ao chat
    addMessageToChat(message, 'user');
    userInput.value = '';
    
    // Mostrar indicador de digitando
    showTypingIndicator();
    
    try {
        // Chamar a API
        const response = await reqApi(message);
        
        // Esconder indicador de digitando
        hideTypingIndicator();
        
        // Adicionar resposta ao chat
        if (response && response.content) {
            const formattedResponse = formatBotResponse(response.content);
            addMessageToChat(formattedResponse, 'bot');
        } else {
            throw new Error('Resposta inválida da API');
        }
    } catch (error) {
        console.error('Erro:', error);
        hideTypingIndicator();
        addMessageToChat('Desculpe, estou com problemas técnicos no momento. Por favor, tente novamente mais tarde.', 'bot');
        
        // Mostrar erro de API
        const apiStatus = document.getElementById('api-status');
        apiStatus.textContent = 'Erro de conexão com o serviço';
        apiStatus.style.display = 'block';
        
        // Esconder o erro após 5 segundos
        setTimeout(() => {
            apiStatus.style.display = 'none';
        }, 5000);
    }
}

async function reqApi(msg) {
    try {
        const context = `Você é MICA, um assistente virtual especializado em apoiar pacientes com câncer e prevenção. 
        Forneça informações confiáveis sobre:
        - Sintomas e prevenção de câncer
        - Apoio psicológico para pacientes
        - Orientação sobre tratamentos
        - Explicação de termos médicos
        - Dicas de cuidados paliativos
        
        Regras importantes:
        ✦ Use **negrito** para títulos e informações importantes
        ✦ Use *itálico* para ênfase
        ✦ Seja empático e acolhedor
        ✦ Não faça diagnósticos
        ✦ Incentive sempre a consultar profissionais
        ✦ Mantenha respostas claras e diretas
        ✦ Responda em português do Brasil`;
        
        const response = await fetch("https://api.groq.com/openai/v1/chat/completions", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Authorization": `Bearer ${API_KEY}`
            },
            body: JSON.stringify({
                messages: [
                    { role: "system", content: context },
                    { role: "user", content: msg }
                ],
                model: "openai/gpt-oss-20b",
                temperature: 0.7,
                max_completion_tokens: 1024,
                top_p: 1,
                stream: false,
                reasoning_effort: "medium",
            })
        });

        if (!response.ok) {
            throw new Error(`Erro na API: ${response.status}`);
        }

        const data = await response.json();
        return data.choices[0].message;
    } catch (error) {
        console.error('Erro na requisição da API:', error);
        throw error;
    }
}

function formatBotResponse(text) {
    // Usa o formatter se estiver disponível, caso contrário usa fallback
    if (typeof chatbotFormatter !== 'undefined') {
        return chatbotFormatter.formatResponse(text);
    }
    
    // Fallback básico
    return fallbackFormatResponse(text);
}

function fallbackFormatResponse(text) {
    if (!text) return "";
    
    let formatted = text.replace(/\s+/g, ' ').trim();
    
    // Converte markdown básico para HTML
    formatted = formatted.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
    formatted = formatted.replace(/\*(.*?)\*/g, '<em>$1</em>');
    
    // Capitaliza primeira letra
    if (formatted && formatted[0]) {
        formatted = formatted[0].toUpperCase() + formatted.slice(1);
    }
    
    // Garante pontuação final
    if (formatted && !['.', '!', '?', ':'].includes(formatted.slice(-1))) {
        formatted += '.';
    }
    
    return formatted;
}

function addMessageToChat(message, sender) {
    const chatMessages = document.getElementById('chat-messages');
    const messageDiv = document.createElement('div');
    messageDiv.classList.add('message', `${sender}-message`);
    
    const messageContent = document.createElement('div');
    messageContent.classList.add('message-content');
    
    const messageParagraph = document.createElement('p');
    
    // Se for mensagem do bot, permite HTML (para formatação)
    // Se for do usuário, trata como texto puro por segurança
    if (sender === 'bot') {
        messageParagraph.innerHTML = message.replace(/\n/g, '<br>');
    } else {
        messageParagraph.textContent = message;
    }
    
    const messageTime = document.createElement('div');
    messageTime.classList.add('message-time');
    messageTime.textContent = getCurrentTime();
    
    messageContent.appendChild(messageParagraph);
    messageDiv.appendChild(messageContent);
    messageDiv.appendChild(messageTime);
    
    // Inserir antes do indicador de digitação
    const typingIndicator = document.getElementById('typing-indicator');
    chatMessages.insertBefore(messageDiv, typingIndicator);
    
    // Rolar para a última mensagem
    chatMessages.scrollTop = chatMessages.scrollHeight;
    
    // Adicionar ao histórico
    chatHistory.push({
        sender: sender,
        message: message,
        time: getCurrentTime()
    });

    // Limitar histórico a 100 mensagens
    if (chatHistory.length > 100) {
        chatHistory = chatHistory.slice(-100);
    }
}

function addQuickQuestions() {
    const quickQuestions = [
        {
            question: "Quais são os sintomas comuns do câncer?",
            icon: "fas fa-map-marker-alt"
        },
        {
            question: "Como posso prevenir o câncer?",
            icon: "fas fa-clock"
        },
        {
            question: "O que é quimioterapia?",
            icon: "fas fa-exclamation-triangle"
        },
        {
            question: "Onde posso encontrar apoio psicológico?",
            icon: "fas fa-fire"
        },
        {
            question: "Quais os tipos de exames de diagnóstico?",
            icon: "fas fa-stethoscope"
        },
        {
            question: "Como funciona a radioterapia?",
            icon: "fas fa-radiation-alt"
        },
        {
            question: "Quais são os fatores de risco?",
            icon: "fas fa-skull-crossbones"
        },
        {
            question: "Como lidar com efeitos colaterais do tratamento?",
            icon: "fas fa-head-side-mask"
        },
        {
            question: "O que é imunoterapia?",
            icon: "fas fa-shield-virus"
        },
        {
            question: "Como ajudar um familiar com câncer?",
            icon: "fas fa-hands-helping"
        },
        {
            question: "Quais direitos do paciente com câncer?",
            icon: "fas fa-balance-scale"
        },
        {
            question: "Alimentação durante o tratamento",
            icon: "fas fa-utensils"
        },
        {
            question: "Quando procurar uma segunda opinião?",
            icon: "fas fa-user-md"
        },
        {
            question: "Como funciona o tratamento cirúrgico?",
            icon: "fas fa-syringe"
        },
        {
            question: "O que esperar da primeira consulta?",
            icon: "fas fa-calendar-check"
        },
        {
            question: "Grupos de apoio e onde encontrar",
            icon: "fas fa-users"
        },
        {
            question: "Como manejar a dor durante o tratamento?",
            icon: "fas fa-pills"
        },
        {
            question: "Cuidados com a saúde mental",
            icon: "fas fa-brain"
        }
    ];

    const quickQuestionsContainer = document.querySelector('.quick-questions');
    if (!quickQuestionsContainer) return;

    quickQuestions.forEach(item => {
        const button = document.createElement('button');
        button.className = 'quick-btn';
        button.setAttribute('data-question', item.question);
        
        button.innerHTML = `
            <i class="${item.icon}"></i>
            <span>${item.question}</span>
        `;
        
        button.addEventListener('click', function() {
            document.getElementById('user-input').value = item.question;
            sendMessage();
        });
        
        quickQuestionsContainer.appendChild(button);
    });
}

function showTypingIndicator() {
    const typingIndicator = document.getElementById('typing-indicator');
    typingIndicator.style.display = 'flex';
    
    // Rolar para o indicador de digitação
    const chatMessages = document.getElementById('chat-messages');
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

function hideTypingIndicator() {
    const typingIndicator = document.getElementById('typing-indicator');
    typingIndicator.style.display = 'none';
}

function toggleVoiceInput() {
    const micBtn = document.getElementById('mic-btn');
    if (!('webkitSpeechRecognition' in window)) {
        addMessageToChat('Seu navegador não suporta reconhecimento de voz.', 'bot');
        return;
    }
    
    if (micBtn.classList.contains('recording')) {
        // Parar gravação
        micBtn.classList.remove('recording');
        addMessageToChat('Recurso de voz em desenvolvimento.', 'bot');
    } else {
        // Iniciar gravação
        micBtn.classList.add('recording');
        addMessageToChat('Recurso de voz em desenvolvimento. Por favor, digite sua mensagem.', 'bot');
    }
}

function getCurrentTime() {
    const now = new Date();
    return now.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
}

function updateMessageTime() {
    const initialMessageTime = document.getElementById('initial-message-time');
    if (initialMessageTime) {
        initialMessageTime.textContent = getCurrentTime();
    }
}

// Exportar funções para uso global
window.chatbot = {
    sendMessage,
    toggleVoiceInput,
    formatBotResponse,
    addMessageToChat
};