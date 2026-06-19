$(document).ready(function() {
    const selectedNumbers = [];
    const numberPrice = parseFloat($('#numberPrice').val());
    const raffleId = $('#raffleId').val();
    const sellNumbersUrl = $('#sellNumbersUrl').val();
    const csrfToken = $('#csrfToken').val();

    // Função para atualizar carrinho
    function updateCart() {
        const cartItems = $('#cartItems');
        const cartActions = $('#cartActions');
        const badgeCartCount = $('#badgeCartCount');
        const quickCartQuantity = $('#quickCartQuantity');
        const quickCartTotal = $('#quickCartTotal');
        
        // Atualizar contadores no resumo rápido
        quickCartQuantity.text(selectedNumbers.length);
        
        if (selectedNumbers.length === 0) {
            cartItems.html(`
                <div class="text-center text-muted py-4">
                    <i class="ti ti-shopping-cart ti-lg mb-2"></i>
                    <p>Nenhum número selecionado</p>
                </div>
            `);
            cartActions.hide();
            badgeCartCount.hide();
            quickCartTotal.text('R$ 0,00');
        } else {
            // Mostrar apenas resumo, sem lista de números
            cartItems.html(`
                <div class="text-center py-3">
                    <i class="ti ti-shopping-cart ti-lg mb-2 text-primary"></i>
                    <p class="mb-0">Você selecionou <strong>${selectedNumbers.length}</strong> número(s)</p>
                </div>
            `);
            
            const total = selectedNumbers.length * numberPrice;
            badgeCartCount.text(selectedNumbers.length).show();
            quickCartTotal.text('R$ ' + total.toFixed(2).replace('.', ','));
            cartActions.show();
        }
    }

    // Selecionar/Desselecionar número
    $(document).on('click', '.number-box', function() {
        // Não permitir selecionar números vendidos
        if ($(this).hasClass('sold')) {
            return;
        }
        
        const numberId = $(this).data('number-id');
        const number = $(this).data('number');
        
        if ($(this).hasClass('selected')) {
            // Remover seleção
            $(this).removeClass('selected').addClass('available');
            const index = selectedNumbers.findIndex(item => item.id === numberId);
            if (index > -1) {
                selectedNumbers.splice(index, 1);
            }
        } else {
            // Adicionar seleção
            $(this).removeClass('available').addClass('selected');
            selectedNumbers.push({ id: numberId, number: number });
        }
        
        updateCart();
    });

    // Processar pagamento
    $('#btnProcessPayment').on('click', function() {
        if (selectedNumbers.length === 0) {
            Swal.fire({
                title: 'Aviso',
                text: 'Selecione pelo menos um número.',
                icon: 'warning',
                confirmButtonColor: '#3085d6'
            });
            return;
        }
        
        const total = selectedNumbers.length * numberPrice;
        
        Swal.fire({
            title: 'Processar Pagamento',
            html: `
                <div class="text-start">
                    <div class="mb-3 p-3 bg-light rounded">
                        <p class="mb-2"><strong>Quantidade:</strong> ${selectedNumbers.length} número(s)</p>
                        <p class="mb-0"><strong>Valor Total:</strong> <span class="text-success fs-5">R$ ${total.toFixed(2).replace('.', ',')}</span></p>
                    </div>
                    <div class="mb-3">
                        <label for="payment-method" class="form-label">Forma de Pagamento <span class="text-danger">*</span></label>
                        <select id="payment-method" class="form-select">
                            <option value="money">Dinheiro</option>
                            <option value="credit_card">Cartão de Crédito</option>
                            <option value="debit_card">Cartão de Débito</option>
                            <option value="pix">PIX</option>
                            <option value="bank_transfer">Transferência Bancária</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="buyer-id-payment" class="form-label">Comprador <span class="text-danger">*</span></label>
                        <select id="buyer-id-payment" class="form-select">
                            <option value="">Selecione o comprador...</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="sale-description" class="form-label">Descrição</label>
                        <input type="text" id="sale-description" class="form-control" maxlength="35" placeholder="Máximo 35 caracteres">
                        <div class="form-text text-end"><span id="sale-description-count">0</span>/35</div>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="ti ti-check"></i> Confirmar Pagamento',
            cancelButtonText: 'Cancelar',
            width: '600px',
            didOpen: () => {
                // Copiar as opções do select de comprador para o modal
                const paymentSelect = $('#buyer-id-payment');
                const buyers = JSON.parse($('#buyers').val() || '[]');
                
                buyers.forEach(buyer => {
                    paymentSelect.append(`<option value="${buyer.id}">${buyer.name}</option>`);
                });

                // Contador de caracteres da descrição
                document.getElementById('sale-description').addEventListener('input', function() {
                    document.getElementById('sale-description-count').textContent = this.value.length;
                });
            },
            preConfirm: () => {
                const paymentMethod = document.getElementById('payment-method').value;
                const buyerId = document.getElementById('buyer-id-payment').value;
                const description = document.getElementById('sale-description').value.trim();
                
                if (!buyerId) {
                    Swal.showValidationMessage('Por favor, selecione o comprador');
                    return false;
                }

                if (description.length > 35) {
                    Swal.showValidationMessage('A descrição deve ter no máximo 35 caracteres');
                    return false;
                }
                
                return {
                    paymentMethod: paymentMethod,
                    buyerId: buyerId,
                    description: description
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Finalizar venda com os dados do modal
                finalizeSale(result.value.paymentMethod, result.value.buyerId, result.value.description);
            }
        });
    });
    
    // Função auxiliar para finalizar venda
    function finalizeSale(paymentMethod = null, buyerId = null, description = null) {
        const numberIds = selectedNumbers.map(item => item.id);
        
        const requestData = {
            _token: csrfToken,
            buyer_id: buyerId,
            number_ids: numberIds
        };
        
        if (paymentMethod) {
            requestData.payment_method = paymentMethod;
        }

        if (description) {
            requestData.description = description;
        }
        
        $.ajax({
            url: sellNumbersUrl,
            method: 'POST',
            data: requestData,
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Sucesso!',
                    text: response.message || 'Venda realizada com sucesso!'
                }).then(() => {
                    window.location.reload();
                });
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: xhr.responseJSON?.message || 'Erro ao processar venda!'
                });
            }
        });
    }

    // Limpar carrinho
    $('#btnClearCart').on('click', function() {
        Swal.fire({
            title: 'Limpar Carrinho?',
            text: 'Todos os números selecionados serão removidos.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, limpar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            // Só limpa o carrinho após confirmação do usuário
            if (result.isConfirmed) {
                // Limpar array de números selecionados
                selectedNumbers.length = 0;
                
                // Retornar números para disponível com background verde (#28a745)
                $('.number-box.selected').removeClass('selected').addClass('available');
                
                // Atualizar o carrinho
                updateCart();
            }
        });
    });

    // Filtro de busca
    $('#searchNumber').on('keyup', function() {
        const searchValue = $(this).val().toLowerCase();
        
        $('.number-item').each(function() {
            const number = $(this).data('number').toString();
            if (number.includes(searchValue)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Filtro de status
    $('#filterStatus').on('change', function() {
        const filterValue = $(this).val();
        
        $('.number-item').each(function() {
            const status = $(this).data('status');
            
            if (filterValue === 'all') {
                $(this).show();
            } else if (filterValue === status) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
});
