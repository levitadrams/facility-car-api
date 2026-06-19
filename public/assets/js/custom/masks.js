$(function () {
    $('.mask-cep, .mask-zipcode').on('change blur', async function (e) {
        const { autofetch, running, lastZipcode } = this.dataset;
        if(autofetch === 'false') return;

        const cep = this.value.replace(/\D/g, '');
        if(cep.length !== 8) return;

        if(lastZipcode === cep) return;
        this.dataset.lastZipcode = cep;

        if(running === 'true') return;
        this.dataset.running = true;

        const $this = $(this);

        try {
            // Usa API interna com dual-provider fallback (BrasilAPI → ViaCEP)
            const response = await fetch(`/api/cep/${cep}`);
            const result = await response.json();

            // CEP encontrado com sucesso
            if(result.valid && result.data) {
                $this.trigger('found.viacep', result.data);
                return;
            }

            // CEP não encontrado - verifica se permite fallback (erro técnico)
            if(result.fallback) {
                // Erro técnico nas APIs - permite entrada manual
                if(typeof createError === 'function') {
                    createError('CEP não pôde ser consultado. Preencha manualmente.');
                }
                $this.trigger('error.viacep');
                return;
            }

            // CEP inválido (não existe)
            if(typeof createError === 'function') {
                createError('CEP não encontrado');
            }
            $this.trigger('not-found.viacep');

        } catch(e) {
            // Erro de rede - permite entrada manual
            if(typeof createError === 'function') {
                createError('Erro ao consultar CEP. Preencha manualmente.');
            }
            $this.trigger('error.viacep');
        } finally {
            delete this.dataset.running;
        }
    }).on('keyup', function ({keyCode}) {
        /**
         * * 48-57: Números
         * * 95-105: Números (NumPad)
         */
        const isValidKey = (48 <= keyCode && keyCode <= 57) || (95 <= keyCode && keyCode <= 105)

        if(!isValidKey) return;

        if(this.value.replace(/\D/g, '').length === 8)
            $(this).trigger('blur');
    }).each(function (i, el) {
        el.dataset.lastZipcode = el.value.replace(/\D/g, '');
    });

    if($.fn.mask !== undefined) {
        // PADRÃO
        $('.mask-numbers').mask('0#');

        // DADOS PESSOAIS
        $('.mask-cpf').mask('000.000.000-00');
        $('.mask-rg').mask('00.000.000-0');
        $('.mask-cnpj').mask('00.000.000/0000-00');

        // TELEFONE
        $('.mask-phone').mask('(00) 0000-0000');
        $('.mask-mobile').mask('(00) 00000-0000');

        // ENDEREÇO
        $('.mask-cep, .mask-zipcode').mask('00000-000');
        $('.mask-uf, .mask-state').mask('SS', {
            onKeyPress: (uf, e) => { e.target.value = uf.toUpperCase() }
        });

        // DATA/HORA
        $('.mask-date').mask('00/00/0000');
        $('.mask-time').mask('00:00:00');
        $('.mask-datetime').mask('00/00/0000 00:00:00')

        // CARTÃO
        $('.mask-card-cvv').mask('0000');
        $('.mask-card-number').mask('0000 0000 0000 0000 000');
    }

    if($.fn.maskMoney !== undefined) {
        // DINHEIRO
        $('.mask-money').maskMoney({
            prefix: 'R$ ',
            decimal: '.',
            thousands: ''
        }).trigger('mask.maskMoney');
        $('.mask-percent').maskMoney({
            decimal: '.',
            suffix: '%'
        }).on('keypress', function () {
            const $this = $(this);
            const percentage = $this.maskMoney('unmasked')[0];
            const max = parseFloat(this.dataset.max || this.max || 100);

            if (percentage > max) {
                $this.maskMoney('mask', max);
            }
        }).trigger('mask.maskMoney');
    }
});
