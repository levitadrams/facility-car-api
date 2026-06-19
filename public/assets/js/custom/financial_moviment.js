$(document).ready(function() {

    // Função para desabilitar todos os elementos de um formulário
    function disableFormElements(divSelector) {
        $(divSelector).find('input, select, textarea, button').attr('disabled', true);
    }

    // Função para desabilitar todos os elementos de um formulário
    function enableeFormElements(divSelector) {
        $(divSelector).find('input, select, textarea, button').attr('disabled', false);
    }

    $('.aption-Radio').on('click', function() {
        // Captura o ID do elemento de rádio clicado
        var radioId = $(this).attr('id');
        // Inicializa o formulário
        initForm(radioId);
    });

    
    //HABILITAR INPUT PESSOA EM RECEITA
    $(document).on('change', '#origin_id', function (e) {
        e.preventDefault();
        var idValue = $(this).val();
        
        if(idValue == 3 || idValue == 4){ // 3 = DÍZIMOS PASTORAIS, 4 = DÍZIMOS DA MEMBRESIA
            $('#person_id').attr('required', true);
            $('#person_id').removeAttr('disabled');
            $('#div-select-person').show();
        }else{
            $('#person_id').removeAttr('required');
            $('#person_id').attr('disabled', true);
            $('#div-select-person').hide();
        }
        
    });

    //HABILITAR OU DESABILITAR INPUT PESSOA EM EDIÇÃO DE RECEITA
    $(document).on('change', '.edit-origin_id', function (e) {
        e.preventDefault();
        var idValue = $(this).val();
        var modal = $(this).closest('.modal');
        var personSelect = modal.find('.edit-person_id');
        var personDiv = modal.find('[id^="edit-select-person-"]');
        
        if(idValue == 3 || idValue == 4){ // 3 = DÍZIMOS PASTORAIS, 4 = DÍZIMOS DA MEMBRESIA
            personSelect.attr('required', true);
            personSelect.removeAttr('disabled');
            personDiv.show();
        }else{
            personSelect.removeAttr('required');
            personSelect.attr('disabled', true);
            personDiv.hide();
        }
        
    });


    $(document).on('change', '.show_balance', function (e) {
        e.preventDefault();
        var selectIdBank = $(this).val();
        var url = getBalanceUrl.replace('/0', '/' + selectIdBank);

        if (selectIdBank) {
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    if (response.balance) {
                        $('.spanTextBalance').text('Saldo: R$ ' + response.balance);
                    } 
                },
                error: function(response) {
                    console.log('Erro ao buscar o saldo');
                }
            });
        } else {
            $('.spanTextBalance').text('Saldo: selecione uma conta');
        }
    });

    //CARREGAR SALDO CASO TENHA CONTA SELECIONADA AO ABRIR A MODAL
    $('.modal').on('show.bs.modal', function () {
        var selectIdBank = $(this).find('.show_balance').val();
        var url = getBalanceUrl.replace('/0', '/' + selectIdBank);
        if (selectIdBank) {
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    if (response.balance) {
                        $('.spanTextBalance').text('Saldo: R$ ' + response.balance);
                    } 
                },
                error: function(response) {
                    console.log('Erro ao buscar o saldo');
                }
            });
        } else {
            $('.spanTextBalance').text('Saldo: selecione uma conta');
        }
    });
        
    

    //RESETAR CAMPOS DO FORMULÁRIOS DA MODAL AO FECHAR
    $('.modal').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
        
        // Resetar campos de pessoa na modal de criação
        $('#person_id').removeAttr('required');
        $('#person_id').attr('disabled', true);
        $('#div-select-person').hide();
        
        // Resetar campos de pessoa nas modais de edição
        $(this).find('.edit-person_id').removeAttr('required');
        $(this).find('.edit-person_id').attr('disabled', true);
        $(this).find('[id^="edit-select-person-"]').hide();
        
        $(this).find('#attached-file').text('');
        $(this).find('#attached-file').hide();
        $(this).find('#btn-attached-file').hide();
    });

    // Função para evitar a seleção da mesma conta  bancária na modal transferência
    $(document).on('change', '#from_account_id, #to_account_id', function() {
        var fromAccount = $('#modal-transfer #from_account_id').val();
        var toAccount = $('#modal-transfer #to_account_id').val();

        if (fromAccount && toAccount && fromAccount === toAccount) {            
            iziToast.warning({
                position: 'center',
                title: 'Atenção!',
                message: 'A conta de origem e a conta de destino não podem ser a mesma.',
                timeout: 10000
            });
            $(this).val('');
            $(this).trigger('change');
        }
    });
});


function editMoviment(id,type) {    
    var url = editMovimentUrl.replace('/0', '/' + id);
    var urlUpdate = updateMovimentUrl.replace('/0', '/' + id);

    var modal = $('#edit-modal-' + id);

    $.ajax({
        url: url,
        type: 'GET',
        success: function(response) {
            switch (type) {
                case 1:
                    modal.find('[id^="origin_id_edit_"]').val(response.origin_id).trigger('change');
                    modal.find('[id^="to_account_id_edit_"]').val(response.to_account_id).trigger('change');
                    modal.find('[id^="description_edit_"]').val(response.description);
                    modal.find('[id^="value_edit_"]').val(response.value);
                    modal.find('[id^="reference_edit_"]').val(response.reference);
                    modal.find('[id^="movement_date_edit_"]').val(response.movement_date);
                    modal.find('[id^="observation_edit_"]').val(response.observation);                    
                    
                    if (response.file) {
                        modal.find('#attached-file').text(response.file.file_name);
                        modal.find('#attached-file').show();
                        modal.find('#btn-attached-file').show();
                    }
                    
                    if(response.person_id != null){
                        modal.find('.edit-person_id').val(response.person_id).trigger('change');
                        modal.find('.edit-person_id').attr('required', true);
                        modal.find('.edit-person_id').removeAttr('disabled');
                        modal.find('[id^="edit-select-person-"]').show();
                    }

                    modal.modal('show');

                    break;
                case 2:
                    modal.find('[id^="from_account_id_edit_"]').val(response.from_account_id).trigger('change');
                    modal.find('[id^="destination_id_edit_"]').val(response.destination_id).trigger('change');
                    modal.find('[id^="description_edit_"]').val(response.description);
                    modal.find('[id^="value_edit_"]').val(response.value);
                    modal.find('[id^="reference_edit_"]').val(response.reference);
                    modal.find('[id^="movement_date_edit_"]').val(response.movement_date);
                    modal.find('[id^="observation_edit_"]').val(response.observation);

                    if (response.file) {
                        modal.find('#attached-file').text(response.file.file_name);
                        modal.find('#attached-file').show();
                        modal.find('#btn-attached-file').show();
                    }

                    modal.modal('show');
                    break;
                case 3:
                    modal.find('[id^="from_account_id_edit_"]').val(response.from_account_id).trigger('change');
                    modal.find('[id^="to_account_id_edit_"]').val(response.to_account_id).trigger('change');
                    modal.find('[id^="description_edit_"]').val(response.description);
                    modal.find('[id^="movement_date_edit_"]').val(response.movement_date);
                    modal.find('[id^="value_edit_"]').val(response.value);
                    modal.find('[id^="observation_edit_"]').val(response.observation);

                    if (response.file) {
                        modal.find('#attached-file').text(response.file.file_name);
                        modal.find('#attached-file').show();
                        modal.find('#btn-attached-file').show();
                    }
                    
                    modal.modal('show');
                    break;
            }
        }     
    });
}

function deleteFile(id,type) {
    var url = deleteFileUrl.replace('/0', '/' + id);
    $.ajax({
        url: url,
        type: 'DELETE',
        success: function(response) {
            switch (type) {
                case 1:
                    $('#attached-file').text('');
                    $('#attached-file').hide();
                    $('#btn-attached-file').hide();
                    break;
                case 2:
                    $('#edit-attached-file').text('');
                    $('#edit-attached-file').hide();
                    $('#edit-btn-attached-file').hide();
                    break;
            }
        },
        error: function(response) {
            console.log(response);
        }
    });
}

function deleteFile(id,type) {

    var url = deleteFileUrl.replace('/0', '/' + id);

    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn',
            cancelButton: 'btn'
        },
        buttonsStyling: true
    });

    swalWithBootstrapButtons.fire({
        title: 'Deseja realmente excluir o arquivo anexo?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sim',
        cancelButtonText: 'Não',
        reverseButtons: true,
        customClass: {
            confirmButton: 'btn btn-confirm-delete',
            cancelButton: 'btn btn-cancel-delete'
        },
    }).then((result) => {
        if (result.isConfirmed) { // se clicou em "confirmar" chama o ajax que faz alteração.
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                   
                    $('#edit-modal' + id).find('#attached-file').text('');
                    $('#edit-modal' + id).find('#attached-file').hide();
                    $('#edit-modal' + id).find('#btn-attached-file').remove();

                    $('#file-td-' + id).empty();                      
                },
                error: function(response) {
                    console.log('Erro ao excluir o arquivo');
                }
            });

        } else if (
            /* Read more about handling dismissals below */
            result.dismiss === Swal.DismissReason.cancel
        ) {
            // NÃO EXIBIRÁ MENSAGEM
        }
    });
}

function deleteItem(id) {
        
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn',
            cancelButton: 'btn'
        },
        buttonsStyling: true
    });

    swalWithBootstrapButtons.fire({
        title: 'Deseja realmente excluir?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sim',
        cancelButtonText: 'Não',
        reverseButtons: true,
        customClass: {
            confirmButton: 'btn btn-confirm-delete',
            cancelButton: 'btn btn-cancel-delete'
        },
    }).then((result) => {
        if (result.isConfirmed) { // se clicou em "confirmar" chama o ajax que faz alteração.
            jQuery("#delete" + id).submit();
        } else if (
            /* Read more about handling dismissals below */
            result.dismiss === Swal.DismissReason.cancel
        ) {
            // NÃO EXIBIRÁ MENSAGEM
        }
    });
}