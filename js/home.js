    document.addEventListener('DOMContentLoaded', function() {
      // Elementos do DOM
      const menuToggle = document.getElementById('menuToggle');
      const mainNav = document.getElementById('mainNav');
      const floatingChatbotBtn = document.getElementById('floatingChatbotBtn');
      const chatbotWidget = document.getElementById('chatbotWidget');
      const closeChatbot = document.getElementById('closeChatbot');
      const userInput = document.getElementById('userInput');
      const sendMessage = document.getElementById('sendMessage');
      const chatBody = document.getElementById('chatBody');
      const backToTop = document.getElementById('backToTop');
      const sections = document.querySelectorAll('.section');
      
      // Menu mobile toggle
      menuToggle.addEventListener('click', function() {
        mainNav.classList.toggle('active');
        menuToggle.innerHTML = mainNav.classList.contains('active') ? 
          '<i class="fas fa-times"></i>' : '<i class="fas fa-bars"></i>';
      });
      
      // Fechar menu ao clicar em um link
      document.querySelectorAll('.nav a').forEach(link => {
        link.addEventListener('click', function() {
          mainNav.classList.remove('active');
          menuToggle.innerHTML = '<i class="fas fa-bars"></i>';
        });
      });
      
      // Chatbot flutuante
      floatingChatbotBtn.addEventListener('click', function() {
        chatbotWidget.classList.add('open');
      });
      
      closeChatbot.addEventListener('click', function() {
        chatbotWidget.classList.remove('open');
      });
   
      
      // Botão voltar ao topo
      window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
          backToTop.classList.add('visible');
        } else {
          backToTop.classList.remove('visible');
        }
      });
      
      backToTop.addEventListener('click', function() {
        window.scrollTo({
          top: 0,
          behavior: 'smooth'
        });
      });
      
      // Animação de scroll para as seções
      function checkScroll() {
        sections.forEach(section => {
          const sectionTop = section.getBoundingClientRect().top;
          const triggerBottom = window.innerHeight * 0.8;
          
          if (sectionTop < triggerBottom) {
            section.classList.add('visible');
          }
        });
      }
      
      window.addEventListener('scroll', checkScroll);
      checkScroll(); // Verificar na carga inicial
      
      // Rolagem suave para âncoras
      document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
          e.preventDefault();
          
          const targetId = this.getAttribute('href');
          if (targetId === '#') return;
          
          const targetElement = document.querySelector(targetId);
          if (targetElement) {
            window.scrollTo({
              top: targetElement.offsetTop - 80,
              behavior: 'smooth'
            });
          }
        });
      });
    });