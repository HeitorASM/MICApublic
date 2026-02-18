document.addEventListener("DOMContentLoaded", () => {

    const atualizacao = document.getElementById("ultimaAtualizacao");
    const agora = new Date().toLocaleString("pt-BR");
    atualizacao.textContent = `Última atualização: ${agora}`;

    const sections = document.querySelectorAll('main section');
    sections.forEach((section, index) => {
        section.style.opacity = '0';
        section.style.transform = 'translateY(20px)';
        section.style.transition = 'all 0.5s ease';
        
        setTimeout(() => {
            section.style.opacity = '1';
            section.style.transform = 'translateY(0)';
        }, 200 * (index + 1));
    });

    const teamTitle = document.querySelector('#equipe h2');
    teamTitle.addEventListener('click', () => {
        teamTitle.style.color = '#FFA000';
        setTimeout(() => {
            teamTitle.style.color = '';
        }, 1000);
    });
});

function highlightCurrentPage() {
    const currentPage = window.location.pathname.split('/').pop();
    const navLinks = document.querySelectorAll('nav a');
    
    navLinks.forEach(link => {
        if (link.getAttribute('href') === currentPage) {
            link.style.backgroundColor = '#FFA000';
            link.style.fontWeight = 'bold';
        }
    });
}

window.onload = highlightCurrentPage;