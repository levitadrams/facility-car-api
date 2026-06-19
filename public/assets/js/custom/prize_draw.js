$(document).ready(function() {
    // ===========================================
    // VARIÁVEIS GLOBAIS
    // ===========================================
    const raffleId = $('#raffleId').val();
    const totalPrizes = parseInt($('#totalPrizes').val());
    const soldNumbers = JSON.parse($('#soldNumbers').val() || '[]');
    const drawNumberUrl = $('#drawNumberUrl').val();
    const finalizeDrawUrl = $('#finalizeDrawUrl').val();
    const csrfToken = $('#csrfToken').val();
    const numberPersonMapJson = $('#numberPersonMap').val();
    const numberPersonMap = numberPersonMapJson ? JSON.parse(numberPersonMapJson) : {};
    const existingWinnersJson = $('#existingWinners').val();
    const existingWinners = existingWinnersJson ? JSON.parse(existingWinnersJson) : {};
    
    // Estado do sorteio
    let drawnNumbers = []; // Números já sorteados
    let winnerPersonIds = []; // IDs de pessoas que já ganharam
    let currentPosition = 1; // Posição atual do sorteio
    let animationInterval = null; // Intervalo da animação
    let isAnimating = false; // Flag de animação ativa
    let isOrderLocked = false; // Flag para bloquear mudança de ordem
    let drawOrder = $('#draw_order_select').val() || 'desc'; // Ordem inicial (lida do select)
    
    // Áudio
    let drumRollAudio = null;
    let isAudioEnabled = true;
    
    // ===========================================
    // FUNÇÕES DE ÁUDIO
    // ===========================================
    
    /**
     * Tocar som de tambor girando (loop)
     */
    function playDrumRoll() {
        if (!isAudioEnabled) return;
        
        // Tentar carregar arquivo de áudio se existir
        try {
            drumRollAudio = new Audio('/assets/sounds/drum.wav');
            drumRollAudio.loop = true;
            drumRollAudio.volume = 0.3;
            drumRollAudio.play().catch(() => {
                console.log('Áudio de tambor não disponível');
            });
        } catch (e) {
            console.log('Arquivo de áudio não encontrado, usando Web Audio API');
            playDrumRollSynthetic();
        }
    }
    
    /**
     * Parar som de tambor
     */
    function stopDrumRoll() {
        if (drumRollAudio) {
            drumRollAudio.pause();
            drumRollAudio.currentTime = 0;
            drumRollAudio = null;
        }
    }
    
    /**
     * Tocar sino/campainha (ao revelar número)
     */
    function playBell() {
        if (!isAudioEnabled) return;
        
        try {
            const bellAudio = new Audio('/assets/sounds/bell.wav');
            bellAudio.volume = 0.5;
            bellAudio.play().catch(() => {
                playBellSynthetic();
            });
        } catch (e) {
            playBellSynthetic();
        }
    }
    
    /**
     * Tocar aplausos (após confirmar ganhador)
     */
    function playApplause() {
        if (!isAudioEnabled) return;
        
        try {
            const applauseAudio = new Audio('/assets/sounds/applause.wav');
            applauseAudio.volume = 0.4;
            applauseAudio.play().catch(() => {
                console.log('Áudio de aplausos não disponível');
            });
        } catch (e) {
            console.log('Arquivo de áudio não encontrado');
        }
    }
    
    /**
     * Som de tambor sintético usando Web Audio API
     */
    function playDrumRollSynthetic() {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        oscillator.frequency.value = 80;
        oscillator.type = 'sine';
        gainNode.gain.value = 0.1;
        
        oscillator.start();
        
        // Guardar para parar depois
        window.drumRollOscillator = { oscillator, audioContext };
    }
    
    /**
     * Parar tambor sintético
     */
    function stopDrumRollSynthetic() {
        if (window.drumRollOscillator) {
            window.drumRollOscillator.oscillator.stop();
            window.drumRollOscillator = null;
        }
    }
    
    /**
     * Sino sintético usando Web Audio API
     */
    function playBellSynthetic() {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        oscillator.frequency.value = 800;
        oscillator.type = 'sine';
        gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
        
        oscillator.start();
        oscillator.stop(audioContext.currentTime + 0.5);
    }
    
    // ===========================================
    // INICIALIZAÇÃO
    // ===========================================
    initializeDraw();
    
    function initializeDraw() {
        // Garantir que drawOrder esteja correto
        drawOrder = $('#draw_order_select').val() || 'desc';
        
        // Carregar ganhadores existentes (se houver)
        loadExistingWinners();
        
        // Habilitar botão de sortear se houver números vendidos
        if (soldNumbers.length > 0 && totalPrizes > 0) {
            $('#btnDraw').prop('disabled', false);
        } else {
            showAlert('Aviso', 'Não há números vendidos suficientes para realizar o sorteio.', 'warning');
        }
        
        // Atualizar contador de sorteados
        updateDrawnCount();
        
        // Determinar posição inicial baseado na ordem
        updateCurrentPosition();
    }
    
    /**
     * Carrega ganhadores já sorteados anteriormente
     */
    function loadExistingWinners() {
        if (Object.keys(existingWinners).length === 0) {
            return; // Não há ganhadores anteriores
        }
        
        // Detectar a ordem dos ganhadores existentes
        const drawnPositions = Object.keys(existingWinners).map(pos => parseInt(pos)).sort((a, b) => a - b);
        
        if (drawnPositions.length >= 2) {
            // Comparar primeira e segunda posição para determinar ordem
            // Se a primeira posição sorteada foi menor que a segunda, é crescente
            // Se a primeira posição sorteada foi maior que a segunda, é decrescente
            const firstPosition = drawnPositions[0];
            const secondPosition = drawnPositions[1];
            
            if (firstPosition < secondPosition) {
                drawOrder = 'asc';
            } else {
                drawOrder = 'desc';
            }
        } else if (drawnPositions.length === 1) {
            // Com apenas um ganhador, verificar se é a primeira ou última posição
            const position = drawnPositions[0];
            if (position === 1) {
                drawOrder = 'asc';
            } else if (position === totalPrizes) {
                drawOrder = 'desc';
            }
            // Se não for nem a primeira nem a última, manter a ordem padrão
        }
        
        // Atualizar o select com a ordem detectada
        $('#draw_order_select').val(drawOrder);
        
        // Desabilitar o select para somente leitura
        $('#draw_order_select').prop('disabled', true);
        
        // Bloquear mudança de ordem se já há ganhadores
        isOrderLocked = true;
        
        console.log('Ordem detectada dos ganhadores existentes:', drawOrder);
        
        // Iterar sobre os ganhadores e preencher os cards
        Object.values(existingWinners).forEach(winner => {
            const position = winner.position;
            const number = winner.number;
            const personId = winner.person_id;
            const personName = winner.person_name;
            
            // Adicionar aos arrays de controle
            drawnNumbers.push(number);
            winnerPersonIds.push(personId);
            
            // Preencher o card de premiação (passando objeto com propriedade name)
            updatePrizeCard(position, number, { name: personName });
        });
        
        // Atualizar contador
        updateDrawnCount();
        
        // Atualizar posição atual para próximo sorteio
        if (drawnPositions.length > 0) {
            if (drawOrder === 'asc') {
                currentPosition = Math.max(...drawnPositions) + 1;
            } else {
                currentPosition = Math.min(...drawnPositions) - 1;
            }
        } else {
            // Nenhum ganhador anterior, começar do início/fim
            currentPosition = drawOrder === 'asc' ? 1 : totalPrizes;
        }
        
        console.log('Posição inicial definida:', currentPosition);
        
        // Verificar se todos os prêmios já foram sorteados
        if (drawnNumbers.length >= totalPrizes) {
            $('#btnDraw').prop('disabled', true);
        }
    }
    
    // ===========================================
    // EVENT LISTENERS
    // ===========================================
    
    // Mudança de ordem do sorteio
    $('#draw_order_select').on('change', function() {
        if (isOrderLocked) {
            Swal.fire({
                icon: 'error',
                title: 'Ordem Bloqueada',
                text: 'Não é possível alterar a ordem após iniciar o sorteio.'
            });
            $(this).val(drawOrder); // Reverter para ordem atual
            return;
        }
        
        drawOrder = $(this).val();
        updateCurrentPosition();
    });
    
    // Botão: Sortear (anima por 7 segundos e sorteia)
    $('#btnDraw').on('click', function() {
        startDrawWithAnimation();
    });
    
    // Botão: Sair do Sorteio
    $('#btnExit').on('click', function() {
        window.location.href = '/admin/sorteios';
    });
    
    // ===========================================
    // FUNÇÕES DE ANIMAÇÃO E SORTEIO
    // ===========================================
    
    /**
     * Inicia a animação por 7 segundos e depois sorteia
     */
    function startDrawWithAnimation() {
        if (drawnNumbers.length >= totalPrizes) {
            showAlert('Sorteio Completo', 'Todos os prêmios já foram sorteados.', 'info');
            return;
        }
        
        if (isAnimating) return;
        
        isAnimating = true;
        
        // Bloquear ordem após iniciar
        if (!isOrderLocked) {
            isOrderLocked = true;
            $('#draw_order_select').prop('disabled', true);
        }
        
        // Desabilitar botões durante animação
        disableAllButtons();
        
        // Tocar som de tambor
        playDrumRoll();
        
        // Destacar prêmio atual
        highlightCurrentPrize();
        
        $('#animationArea p').text('Sorteio em andamento...');
        
        // PASSO 1: Sortear o número ANTES da animação
        const drawnNumber = getRandomAvailableNumber();
        
        if (drawnNumber === null) {
            showAlert('Erro', 'Não há números disponíveis para sortear.', 'error');
            resetButtons();
            isAnimating = false;
            return;
        }
        
        // Adicionar aos números sorteados
        drawnNumbers.push(drawnNumber);
        
        console.log('Número sorteado:', drawnNumber);
        
        // Obter os dígitos do número sorteado
        const targetDigits = formatNumber(drawnNumber).split('').map(d => parseInt(d));
        
        // PASSO 2: Iniciar animação de rolagem vertical
        startSlotMachineAnimation(targetDigits, drawnNumber);
    }
    
    /**
     * Animação de slot machine vertical
     */
    function startSlotMachineAnimation(targetDigits, drawnNumber) {
        const $columns = $('.slot-column');
        const rollDuration = 1800; // 1.8 segundos de rolagem
        const stopDelay = 500; // 500ms entre parada de cada coluna
        // Total: 1.8s rolagem + (4 colunas * 0.5s) = 3.8s
        
        // Iniciar rolagem rápida em todas as colunas
        $columns.each(function(index) {
            const $column = $(this);
            const $numbers = $column.find('.slot-numbers');
            
            // Remover classe stopped se existir
            $column.removeClass('stopped');
            
            // Posição inicial aleatória
            let position = Math.floor(Math.random() * 10) * 150;
            $numbers.css('transform', `translateY(-${position}px)`);
            
            // Animação de rolagem rápida
            let speed = 30; // velocidade inicial (ms)
            const rollInterval = setInterval(() => {
                position += 150; // Mover para próximo número
                if (position >= 1650) { // 11 números * 150px
                    position = 0;
                }
                $numbers.css('transform', `translateY(-${position}px)`);
            }, speed);
            
            // Guardar referência do intervalo
            $column.data('rollInterval', rollInterval);
        });
        
        // Parar cada coluna em sequência após 1.8 segundos
        setTimeout(() => {
            stopColumnsInSequence(targetDigits, drawnNumber);
        }, rollDuration);
    }
    
    /**
     * Para as colunas em sequência com intervalo
     */
    function stopColumnsInSequence(targetDigits, drawnNumber) {
        const $columns = $('.slot-column');
        const stopDelay = 500; // 500ms entre cada coluna
        
        $columns.each(function(index) {
            const $column = $(this);
            const targetDigit = targetDigits[index];
            
            // Delay progressivo para cada coluna
            setTimeout(() => {
                stopColumn($column, targetDigit);
                
                // Se é a última coluna, processar vitória
                if (index === $columns.length - 1) {
                    setTimeout(() => {
                        onAllColumnsStopped(drawnNumber);
                    }, 800);
                }
            }, index * stopDelay);
        });
    }
    
    /**
     * Para uma coluna específica no dígito alvo
     */
    function stopColumn($column, targetDigit) {
        // Parar intervalo de rolagem
        const rollInterval = $column.data('rollInterval');
        if (rollInterval) {
            clearInterval(rollInterval);
        }
        
        const $numbers = $column.find('.slot-numbers');
        
        // Calcular posição final (targetDigit * 150px)
        const finalPosition = targetDigit * 150;
        
        // Aplicar classe stopped para transição suave
        $column.addClass('stopped');
        
        // Mover para posição final
        $numbers.css('transform', `translateY(-${finalPosition}px)`);
    }
    
    /**
     * Chamado quando todas as colunas pararam
     */
    function onAllColumnsStopped(drawnNumber) {
        // Parar sons
        stopDrumRoll();
        stopDrumRollSynthetic();
        playBell();
        
        isAnimating = false;
        
        $('#animationArea p').text('Número sorteado!');
        
        // Buscar dados do ganhador
        fetchWinnerAndUpdatePrize(drawnNumber, currentPosition);
    }
    
    /**
     * Buscar dados do ganhador e atualizar o card do prêmio
     */
    function fetchWinnerAndUpdatePrize(number, position) {
        // Desabilitar botões durante o processo
        disableAllButtons();
        
        // Validar dados antes de enviar
        const dataToSend = {
            _token: csrfToken,
            number: parseInt(number),
            position: parseInt(position),
            draw_order: drawOrder
        };
        
        console.log('Enviando dados para sorteio:', {
            url: drawNumberUrl,
            data: dataToSend
        });
        
        // Verificar se os valores são válidos
        if (!dataToSend.number || !dataToSend.position || !dataToSend.draw_order) {
            console.error('Dados inválidos:', dataToSend);
            Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: 'Dados inválidos para o sorteio. Verifique o console.'
            });
            resetButtons();
            return;
        }
        
        $.ajax({
            url: drawNumberUrl,
            method: 'POST',
            data: dataToSend,
            success: function(response) {
                console.log('Resposta do servidor:', response);
                if (response.success) {
                    // Adicionar person_id à lista de ganhadores
                    if (response.winner.person_id) {
                        winnerPersonIds.push(response.winner.person_id);
                    }
                    
                    // Atualizar card do prêmio
                    updatePrizeCard(position, number, response.winner);
                    
                    // Atualizar contador
                    updateDrawnCount();
                    
                    // Verificar se é o último sorteio ANTES de exibir o modal
                    const isLastDraw = drawnNumbers.length >= totalPrizes;

                    // Exibir SweetAlert2 com o ganhador
                    console.log('Chamando showWinnerAlert com:', {position, number, winner: response.winner, isLastDraw});
                    showWinnerAlert(position, number, response.winner, isLastDraw);
                    
                    // Verificar se todos foram sorteados
                    if (isLastDraw) {
                        // Sorteio completo - aguardar confirmação explícita do usuário no modal
                        $('#animationArea p').text('Sorteio concluído!');
                        $('#btnDraw').prop('disabled', true);
                        // Bloquear saída enquanto a finalização não for concluída
                        $('#btnExit').prop('disabled', true);
                    } else {
                        // Avançar para próxima posição
                        advancePosition();
                        
                        // Resetar botões para próximo sorteio após 7 segundos
                        setTimeout(function() {
                            resetButtons();
                            resetSlotMachine();
                            $('#animationArea p').text('Aguardando próximo sorteio');
                        }, 7000);
                    }
                } else {
                    // Se a pessoa já ganhou, tentar sortear novamente automaticamente
                    if (response.already_won) {
                        showAlert('Aviso', response.message, 'warning');
                        // Tentar sortear outro número automaticamente
                        setTimeout(function() {
                            const newNumber = getRandomAvailableNumber();
                            if (newNumber !== null) {
                                $('#animatedNumber span').text(formatNumber(newNumber));
                                fetchWinnerAndUpdatePrize(newNumber, position);
                            } else {
                                showAlert('Erro', 'Não há mais números disponíveis para sortear.', 'error');
                                resetButtons();
                            }
                        }, 1000);
                    } else {
                        showAlert('Erro', response.message || 'Erro ao processar sorteio', 'error');
                        resetButtons();
                    }
                }
            },
            error: function(xhr) {
                console.error('Erro na requisição:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    response: xhr.responseJSON,
                    responseText: xhr.responseText
                });
                
                const response = xhr.responseJSON;
                // Se a pessoa já ganhou, tentar sortear novamente automaticamente
                if (response?.already_won) {
                    showAlert('Aviso', response.message, 'warning');
                    setTimeout(function() {
                        const newNumber = getRandomAvailableNumber();
                        if (newNumber !== null) {
                            $('#animatedNumber span').text(formatNumber(newNumber));
                            fetchWinnerAndUpdatePrize(newNumber, position);
                        } else {
                            showAlert('Erro', 'Não há mais números disponíveis para sortear.', 'error');
                            resetButtons();
                        }
                    }, 1000);
                } else {
                    // Mostrar erro de validação se houver
                    let errorMessage = 'Erro ao processar sorteio';
                    if (response?.message) {
                        errorMessage = response.message;
                    } else if (response?.errors) {
                        // Erros de validação Laravel
                        const errors = Object.values(response.errors).flat();
                        errorMessage = errors.join('<br>');
                    } else if (xhr.status === 422) {
                        errorMessage = 'Erro de validação. Verifique os dados enviados.';
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        html: errorMessage
                    });
                    resetButtons();
                }
            }
        });
    }
    
    // ===========================================
    // FUNÇÕES DE CONTROLE
    // ===========================================
    
    /**
     * Atualizar posição atual baseado na ordem
     */
    function updateCurrentPosition() {
        if (drawnNumbers.length === 0) {
            currentPosition = drawOrder === 'asc' ? 1 : totalPrizes;
        }
    }
    
    /**
     * Avançar para próxima posição baseado na ordem
     */
    function advancePosition() {
        if (drawOrder === 'asc') {
            currentPosition++;
        } else {
            currentPosition--;
        }
    }
    
    /**
     * Destacar prêmio atual
     */
    function highlightCurrentPrize() {
        $('.prize-card, .prize-card-compact').removeClass('current-prize');
        $(`.prize-card[data-position="${currentPosition}"], .prize-card-compact[data-position="${currentPosition}"]`).addClass('current-prize');
    }
    
    /**
     * Atualizar card do prêmio com dados do ganhador
     */
    function updatePrizeCard(position, number, winner) {
        const $card = $(`.prize-card[data-position="${position}"], .prize-card-compact[data-position="${position}"]`);
        
        $card.find('.number-value, .number-value-compact').text(formatNumber(number));
        $card.find('.name-value, .name-value-compact').text(winner.name);
        $card.addClass('winner-drawn').removeClass('current-prize');
        
        // Adicionar efeito de confete
        $card.addClass('prize-won');
        setTimeout(function() {
            $card.removeClass('prize-won');
        }, 1000);
    }
    
    /**
     * Atualizar contador de sorteados
     */
    function updateDrawnCount() {
        $('#drawnCountDisplay').text(drawnNumbers.length);
    }
    
    /**
     * Obter número aleatório disponível
     * Exclui números já sorteados e números de pessoas que já ganharam
     */
    function getRandomAvailableNumber() {
        const availableNumbers = soldNumbers.filter(num => {
            // Excluir números já sorteados
            if (drawnNumbers.includes(num)) {
                return false;
            }
            
            // Excluir números de pessoas que já ganharam
            const personId = numberPersonMap[num];
            if (personId && winnerPersonIds.includes(personId)) {
                return false;
            }
            
            return true;
        });
        
        if (availableNumbers.length === 0) {
            return null;
        }
        
        const randomIndex = Math.floor(Math.random() * availableNumbers.length);
        return availableNumbers[randomIndex];
    }
    
    /**
     * Formatar número com zeros à esquerda
     */
    function formatNumber(number) {
        return String(number).padStart(4, '0');
    }
    
    /**
     * Resetar display de slot machine
     */
    function resetSlotMachine() {
        $('.slot-column').each(function() {
            const $column = $(this);
            const $numbers = $column.find('.slot-numbers');
            
            // Parar qualquer animação
            const rollInterval = $column.data('rollInterval');
            if (rollInterval) {
                clearInterval(rollInterval);
            }
            
            // Remover classe stopped
            $column.removeClass('stopped');
            
            // Resetar posição
            $numbers.css('transform', 'translateY(0)');
        });
    }
    
    // ===========================================
    // FUNÇÕES DE CONTROLE DE BOTÕES
    // ===========================================
    
    function resetButtons() {
        $('#btnDraw').prop('disabled', false);
        $('#btnFinalize').prop('disabled', true);
    }
    
    function disableAllButtons() {
        $('#btnDraw').prop('disabled', true);
        $('#btnFinalize').prop('disabled', true);
    }
    
    // ===========================================
    // FINALIZAÇÃO DO SORTEIO
    // ===========================================
    
    /**
     * Auto-finalizar sorteio após último sorteio (sem sair da tela)
     */
    function autoFinalizeDraw() {
        disableAllButtons();
        
        Swal.fire({
            title: 'Finalizando...',
            text: 'Por favor, aguarde.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        $.ajax({
            url: finalizeDrawUrl,
            method: 'POST',
            data: {
                _token: csrfToken,
                draw_order: drawOrder
            },
            success: function(response) {
                if (response.success) {
                    // Contar participantes únicos contemplados
                    const uniqueWinners = [...new Set(winnerPersonIds)].length;
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Sorteio Finalizado!',
                        html: `
                            <p>${response.message || 'O sorteio foi finalizado com sucesso.'}</p>
                            <p class="mt-3"><strong>Total de contemplados: ${uniqueWinners}</strong></p>
                        `,
                        confirmButtonText: 'OK',
                        allowOutsideClick: false
                    });
                    
                    // Atualizar interface (não redireciona)
                    $('#animationArea p').text('Sorteio finalizado com sucesso!');
                    $('#btnDraw').prop('disabled', true);
                    $('#btnExit').prop('disabled', false);
                } else {
                    showAlert('Erro', response.message || 'Erro ao finalizar sorteio', 'error');
                }
            },
            error: function(xhr) {
                showAlert('Erro', xhr.responseJSON?.message || 'Erro ao finalizar sorteio', 'error');
            }
        });
    }
    
    // ===========================================
    // FUNÇÕES AUXILIARES
    // ===========================================
    
    /**
     * Exibir SweetAlert2 com destaque do ganhador (7 segundos)
     */
    function showWinnerAlert(position, number, winner, isLastDraw) {
        console.log('showWinnerAlert chamada - Parâmetros:', {position, number, winner, isLastDraw});
        
        // Buscar dados do prêmio
        const prizeCard = $(`.prize-card-compact[data-position="${position}"]`);
        const prizeDescription = prizeCard.find('.prize-description-compact').text() || 'Prêmio';
        
        console.log('Prêmio encontrado:', prizeDescription);
        console.log('Chamando Swal.fire...');
        
        const swalConfig = {
            icon: 'success',
            title: `<i class="ti ti-trophy"></i> ${position}º Lugar`,
            html: `
                <div style="padding: 20px;">
                    <p style="color: #6c757d; margin-bottom: 15px;">${prizeDescription}</p>
                    <div style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 12px; padding: 30px; margin: 20px 0; border: 2px solid #dee2e6;">
                        <h2 style="color: #696cff; font-size: 3.5rem; font-weight: bold; margin-bottom: 10px; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);">
                            ${formatNumber(number)}
                        </h2>
                        <small style="color: #6c757d;">Número Sorteado</small>
                    </div>
                    <div style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border-radius: 12px; padding: 25px; margin-top: 20px;">
                        <h4 style="color: white; margin-bottom: 10px;">
                            <i class="ti ti-confetti"></i> Parabéns!
                        </h4>
                        <h3 style="color: white; font-weight: bold; font-size: 2rem; margin: 0;">
                            ${winner.name || 'Não identificado'}
                        </h3>
                    </div>
                </div>
            `,
            allowOutsideClick: false,
            allowEscapeKey: false,
            width: '600px',
            customClass: {
                popup: 'winner-alert-popup'
            }
        };

        if (isLastDraw) {
            // Último sorteio: exigir confirmação explícita do usuário
            swalConfig.showConfirmButton = true;
            swalConfig.confirmButtonText = '<i class="ti ti-check me-1"></i> Finalizar Sorteio';
            swalConfig.confirmButtonColor = '#28a745';
        } else {
            // Sorteios intermediários: fechar automaticamente após 4 segundos
            swalConfig.showConfirmButton = false;
            swalConfig.timer = 4000;
            swalConfig.timerProgressBar = true;
        }

        Swal.fire(swalConfig).then((result) => {
            console.log('Swal.fire finalizado:', result);
            
            // Tocar aplausos quando o modal fechar
            playApplause();

            // Se for o último sorteio, iniciar finalização após confirmação do usuário
            if (isLastDraw) {
                autoFinalizeDraw();
            }
        });
    }
    
    function showAlert(title, text, icon) {
        Swal.fire({
            icon: icon,
            title: title,
            text: text
        });
    }
});
