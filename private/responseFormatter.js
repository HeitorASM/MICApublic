// private/responseFormatter.js

class ResponseFormatter {
    constructor(maxLineLength = 80) {
        this.maxLineLength = maxLineLength;
    }

    formatResponse(text) {
        if (!text || typeof text !== 'string') {
            return "";
        }

        // 1. Aplica formatação markdown (negrito, itálico, etc)
        let formatted = this._parseMarkdown(text);

        // 2. Remove espaços excessivos
        formatted = formatted.replace(/\s+/g, ' ').trim();

        // 3. Decodifica entidades HTML básicas
        formatted = this._decodeHtmlEntities(formatted);

        // 4. Corrige pontuação
        formatted = this._fixPunctuation(formatted);

        // 5. Capitaliza primeira letra
        formatted = this._capitalizeFirstLetter(formatted);

        // 6. Quebra em linhas para melhor legibilidade
        formatted = this._wrapText(formatted);

        return formatted;
    }

    _parseMarkdown(text) {
        // Converte **texto** para <strong>texto</strong>
        text = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        
        // Converte *texto* para <em>texto</em>
        text = text.replace(/\*(.*?)\*/g, '<em>$1</em>');
        
        // Converte _texto_ para <em>texto</em>
        text = text.replace(/_(.*?)_/g, '<em>$1</em>');
        
        // Converte `texto` para <code>texto</code>
        text = text.replace(/`(.*?)`/g, '<code>$1</code>');
        
        return text;
    }

    _decodeHtmlEntities(text) {
        const entities = {
            '&amp;': '&',
            '&lt;': '<',
            '&gt;': '>',
            '&quot;': '"',
            '&#39;': "'",
            '&nbsp;': ' '
        };

        return text.replace(/&amp;|&lt;|&gt;|&quot;|&#39;|&nbsp;/g, match => entities[match]);
    }

    _fixPunctuation(text) {
        if (!text) return text;

        // Remove espaços antes de pontuação
        let formatted = text.replace(/\s+([.,!?;:])/g, '$1');

        // Adiciona espaço após pontuação quando necessário
        formatted = formatted.replace(/([.,!?;:])(?=\w)/g, '$1 ');

        // Garante pontuação final
        if (formatted && !['.', '!', '?', ':'].includes(formatted.slice(-1))) {
            formatted += '.';
        }

        return formatted;
    }

    _capitalizeFirstLetter(text) {
        if (!text) return text;

        const lines = text.split('\n');
        const formattedLines = lines.map(line => {
            if (line && line[0] && !line.startsWith('<')) {
                return line[0].toUpperCase() + line.slice(1);
            }
            return line;
        });

        return formattedLines.join('\n');
    }

    _wrapText(text) {
        // Não quebra linhas que contêm HTML para não quebrar tags
        if (text.includes('<')) {
            return text;
        }

        const words = text.split(' ');
        if (words.length <= 1) return text;

        let lines = [];
        let currentLine = '';

        words.forEach(word => {
            if ((currentLine + word).length > this.maxLineLength) {
                if (currentLine) {
                    lines.push(currentLine.trim());
                }
                currentLine = word + ' ';
            } else {
                currentLine += word + ' ';
            }
        });

        if (currentLine.trim()) {
            lines.push(currentLine.trim());
        }

        return lines.join('\n');
    }

    formatForHtml(text) {
        const formatted = this.formatResponse(text);
        return formatted.replace(/\n/g, '<br>');
    }
}

// Instância global para uso fácil
const chatbotFormatter = new ResponseFormatter();

// Função de conveniência para formatação rápida
function formatChatbotResponse(text, maxLineLength = 80) {
    const formatter = new ResponseFormatter(maxLineLength);
    return formatter.formatResponse(text);
}

// Testes da formatação markdown
if (typeof window !== 'undefined' && window.location.href.includes('debug')) {
    console.log('=== TESTES DO FORMATADOR MARKDOWN ===');
    
    const testCases = [
        "**Texto em negrito** e texto normal",
        "**Afaste-se imediatamente** Saia do local",
        "*Texto em itálico* e **negrito**",
        "Lista: **1. Item um** **2. Item dois**",
        "`código` e **destaque**"
    ];

    testCases.forEach((test, index) => {
        console.log(`\nTeste ${index + 1}:`);
        console.log('Original:', test);
        console.log('Formatado:', formatChatbotResponse(test));
        console.log('---');
    });
}