document.addEventListener('DOMContentLoaded', function() {
    const numbersToDraw = document.getElementById('numbers_to_draw');
    const btnSubmitForm = document.getElementById('btn_submit_form');
    const prizesColumn1 = document.getElementById('prizesColumn1');
    const prizesColumn2 = document.getElementById('prizesColumn2');
    const noPrizesMessage = document.getElementById('noPrizesMessage');
    
    // Inicializar campos de premiação se vier valor do servidor
    const initialValue = parseInt(numbersToDraw.value) || 0;
    if (initialValue > 0) {
        for (let i = 1; i <= initialValue; i++) {
            createPrizeField(i);
        }
        updateSubmitButton();
    } else {
        showNoPrizesMessage();
    }
    
    // Validação do formulário antes de submeter
    document.querySelector('form').addEventListener('submit', function(e) {
        const quantity = parseInt(numbersToDraw.value);
        
        if (quantity < 1) {
            e.preventDefault();
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Campos Obrigatórios',
                    text: 'Por favor, informe quantos números serão sorteados.',
                    confirmButtonText: 'Entendi',
                    confirmButtonColor: '#3085d6'
                });
            } else {
                alert('Por favor, informe quantos números serão sorteados.');
            }
            
            return false;
        }
        
        // Contar quantos campos de premiação existem
        let prizeFieldsCount = 0;
        for (let i = 1; i <= 10; i++) {
            const prizeField = document.getElementById('prize_' + i);
            if (prizeField) {
                prizeFieldsCount++;
            }
        }
        
        // Verificar se a quantidade de números a sortear corresponde aos campos criados
        if (quantity !== prizeFieldsCount) {
            e.preventDefault();
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro de Validação',
                    html: `<p>O campo <strong>"Números a Sortear"</strong> está configurado para <strong>${quantity}</strong>, mas existem <strong>${prizeFieldsCount}</strong> campo(s) de premiação criado(s).</p>
                           <p class="mt-2">Use os botões + e - para ajustar a quantidade de prêmios.</p>`,
                    confirmButtonText: 'Entendi',
                    confirmButtonColor: '#3085d6'
                });
            } else {
                alert(`O campo "Números a Sortear" está configurado para ${quantity}, mas existem ${prizeFieldsCount} campo(s) de premiação criado(s).\n\nUse os botões + e - para ajustar.`);
            }
            
            return false;
        }
    });
});

/**
 * Atualiza o valor do campo "Números a Sortear" e gerencia os campos de premiação
 * @param {number} change - Valor de incremento (1) ou decremento (-1)
 */
function updateNumbersToDraw(change) {
    const numbersToDraw = document.getElementById('numbers_to_draw');
    const currentValue = parseInt(numbersToDraw.value) || 0;
    const newValue = currentValue + change;
    
    // Validar limites
    if (newValue < 0) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Limite Mínimo',
                text: 'O valor não pode ser menor que 0.',
                timer: 2000,
                showConfirmButton: false
            });
        }
        return;
    }
    
    if (newValue > 10) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Limite Máximo',
                text: 'O máximo de prêmios permitido é 10.',
                timer: 2000,
                showConfirmButton: false
            });
        }
        return;
    }
    
    // Atualizar valor
    numbersToDraw.value = newValue;
    
    // Adicionar ou remover campo de premiação
    if (change > 0) {
        // Incremento: criar novo campo
        createPrizeField(newValue);
    } else if (change < 0) {
        // Decremento: remover último campo
        removePrizeField(currentValue);
    }
    
    // Atualizar botão de submit
    updateSubmitButton();
}

/**
 * Cria um novo campo de premiação
 * @param {number} position - Posição do prêmio (1 a 10)
 */
function createPrizeField(position) {
    // Esconder mensagem de "sem prêmios"
    const noPrizesMessage = document.getElementById('noPrizesMessage');
    if (noPrizesMessage) {
        noPrizesMessage.style.display = 'none';
    }
    
    // Determinar em qual coluna adicionar (ímpares na primeira, pares na segunda)
    const column = position % 2 !== 0 ? document.getElementById('prizesColumn1') : document.getElementById('prizesColumn2');
    
    // Criar wrapper do campo
    const wrapper = document.createElement('div');
    wrapper.className = 'mb-3 prize-field-wrapper';
    wrapper.id = `prize_wrapper_${position}`;
    
    // Criar label
    const label = document.createElement('label');
    label.className = 'form-label';
    label.htmlFor = `prize_${position}`;
    label.innerHTML = `<strong>${position}º Lugar</strong> <span class="text-danger">*</span>`;
    
    // Criar input
    const input = document.createElement('input');
    input.type = 'text';
    input.className = 'form-control prize-field';
    input.id = `prize_${position}`;
    input.name = `prize[${position}]`;
    input.placeholder = 'Descrição do prêmio';
    input.required = true;
    
    // Montar estrutura
    wrapper.appendChild(label);
    wrapper.appendChild(input);
    column.appendChild(wrapper);
    
    // Focar no campo recém-criado
    setTimeout(() => input.focus(), 100);
}

/**
 * Remove um campo de premiação específico
 * @param {number} position - Posição do prêmio a ser removido
 */
function removePrizeField(position) {
    const wrapper = document.getElementById(`prize_wrapper_${position}`);
    
    if (wrapper) {
        // Adicionar classe de animação
        wrapper.classList.add('removing');
        
        // Remover após animação
        setTimeout(() => {
            wrapper.remove();
            
            // Verificar se não há mais campos e mostrar mensagem
            const prizesColumn1 = document.getElementById('prizesColumn1');
            const prizesColumn2 = document.getElementById('prizesColumn2');
            
            if (prizesColumn1.children.length === 0 && prizesColumn2.children.length === 0) {
                showNoPrizesMessage();
            }
        }, 300);
    }
}

/**
 * Mostra mensagem quando não há prêmios configurados
 */
function showNoPrizesMessage() {
    const noPrizesMessage = document.getElementById('noPrizesMessage');
    if (noPrizesMessage) {
        noPrizesMessage.style.display = 'block';
    }
}

/**
 * Atualiza o estado do botão de submit baseado na quantidade de prêmios
 */
function updateSubmitButton() {
    const btnSubmitForm = document.getElementById('btn_submit_form');
    const numbersToDraw = document.getElementById('numbers_to_draw');
    const quantity = parseInt(numbersToDraw.value) || 0;
    
    if (quantity > 0) {
        btnSubmitForm.disabled = false;
        btnSubmitForm.classList.remove('disabled');
        btnSubmitForm.title = '';
    } else {
        btnSubmitForm.disabled = true;
        btnSubmitForm.classList.add('disabled');
        btnSubmitForm.title = 'Configure pelo menos 1 prêmio antes de criar o sorteio';
    }
}
