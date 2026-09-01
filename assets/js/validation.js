document.addEventListener('DOMContentLoaded', function () {
    var forms = document.querySelectorAll('form[data-validate]');

    function onlyDigits(value) {
        return value.replace(/\D+/g, '');
    }

    function isValidCPF(cpf) {
        cpf = onlyDigits(cpf);

        if (cpf.length !== 11 || /^(\d)\1{10}$/.test(cpf)) {
            return false;
        }

        for (var length = 9; length < 11; length++) {
            var sum = 0;

            for (var i = 0; i < length; i++) {
                sum += parseInt(cpf.charAt(i), 10) * ((length + 1) - i);
            }

            var digit = ((10 * sum) % 11) % 10;
            if (digit !== parseInt(cpf.charAt(length), 10)) {
                return false;
            }
        }

        return true;
    }

    var cpfInput = document.querySelector('input[data-mask="cpf"]');
    if (cpfInput) {
        cpfInput.addEventListener('input', function () {
            var digits = onlyDigits(cpfInput.value).slice(0, 11);
            cpfInput.value = digits;
        });

        cpfInput.addEventListener('blur', function () {
            if (cpfInput.value && !isValidCPF(cpfInput.value)) {
                cpfInput.setCustomValidity('CPF invalido.');
            } else {
                cpfInput.setCustomValidity('');
            }
        });
    }

    var phoneInput = document.querySelector('input[data-mask="telefone"]');
    if (phoneInput) {
        phoneInput.addEventListener('input', function () {
            phoneInput.value = onlyDigits(phoneInput.value).slice(0, 11);
        });
    }

    var plateInput = document.querySelector('input[data-mask="placa"]');
    if (plateInput) {
        plateInput.addEventListener('input', function () {
            plateInput.value = plateInput.value.replace(/[^A-Za-z0-9]/g, '').toUpperCase().slice(0, 7);
        });

        plateInput.addEventListener('blur', function () {
            var plateRegex = /^[A-Z]{3}[0-9][A-Z0-9][0-9]{2}$/;
            if (plateInput.value && !plateRegex.test(plateInput.value)) {
                plateInput.setCustomValidity('Placa invalida. Use formato ABC1D23 ou ABC1234.');
            } else {
                plateInput.setCustomValidity('');
            }
        });
    }

    var retiradaInput = document.getElementById('data_retirada');
    var entregaInput = document.getElementById('data_entrega');

    function validateRentalDates() {
        if (!retiradaInput || !entregaInput) {
            return;
        }

        if (retiradaInput.value && entregaInput.value && entregaInput.value < retiradaInput.value) {
            entregaInput.setCustomValidity('A data de entrega deve ser maior ou igual a data de retirada.');
        } else {
            entregaInput.setCustomValidity('');
        }
    }

    if (retiradaInput && entregaInput) {
        retiradaInput.addEventListener('change', validateRentalDates);
        entregaInput.addEventListener('change', validateRentalDates);
    }

    forms.forEach(function (form) {
        form.addEventListener('submit', function (event) {
            validateRentalDates();

            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }

            form.classList.add('was-validated');
        });
    });
});
