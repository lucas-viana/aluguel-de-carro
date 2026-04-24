USE aluguel_veiculos;

START TRANSACTION;

SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE alugueis;
TRUNCATE TABLE veiculos;
TRUNCATE TABLE usuarios;
SET FOREIGN_KEY_CHECKS = 1;

INSERT INTO usuarios (id, nome_completo, cpf, data_nascimento, telefone, email, senha, endereco, tipo) VALUES
(1,  'Administrador',  '00000000191', '1990-01-01', '11900000000', 'admin@rentcar.com',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sede RentCar - Sao Paulo/SP',          'admin'),
(2,  'Lucas Almeida',  '52998224725', '1991-04-10', '11987654321', 'lucas.almeida@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Rua das Acacias, 120 - Sao Paulo/SP',  'comum'),
(3,  'Mariana Costa',  '12345678909', '1994-11-22', '11991234567', 'mariana.costa@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Av. Paulista, 900 - Sao Paulo/SP',     'comum'),
(4,  'Rafael Souza',   '11144477735', '1988-02-03', '21999887766', 'rafael.souza@email.com',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Rua Voluntarios da Patria, 45 - Rio de Janeiro/RJ', 'comum'),
(5,  'Camila Pereira', '86288366757', '1996-07-15', '31988776655', 'camila.pereira@email.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Rua da Bahia, 1500 - Belo Horizonte/MG','comum'),
(6,  'Bruno Martins',  '93541134780', '1990-09-28', '41977665544', 'bruno.martins@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Rua XV de Novembro, 222 - Curitiba/PR','comum'),
(7,  'Ana Beatriz Lima','29537914800','1999-01-19', '71966554433', 'ana.lima@email.com',      '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Rua Chile, 340 - Salvador/BA',         'comum');

INSERT INTO veiculos (id, modelo, cor, fabricante, placa, disponivel) VALUES
(1, 'Corolla XEi', 'Prata', 'Toyota', 'ABC1D23', 0),
(2, 'Onix LT', 'Branco', 'Chevrolet', 'DEF2E34', 1),
(3, 'HB20 Comfort', 'Preto', 'Hyundai', 'GHI3F45', 1),
(4, 'Compass Longitude', 'Cinza', 'Jeep', 'JKL4G56', 0),
(5, 'Polo Highline', 'Azul', 'Volkswagen', 'MNO5H67', 1),
(6, 'Kicks Sense', 'Vermelho', 'Nissan', 'PQR6I78', 1),
(7, 'Civic EXL', 'Prata', 'Honda', 'STU7J89', 0),
(8, 'T-Cross Comfortline', 'Branco', 'Volkswagen', 'VWX8K90', 1);

INSERT INTO alugueis (id, data_retirada, data_entrega, forma_pagamento, usuario_id, veiculo_id, status, finalizado_em) VALUES
(1, '2026-02-02', '2026-02-06', 'credito', 1, 3, 'finalizado', '2026-02-06 11:20:00'),
(2, '2026-02-20', '2026-02-24', 'pix', 2, 5, 'finalizado', '2026-02-24 17:40:00'),
(3, '2026-03-27', '2026-04-02', 'debito', 3, 1, 'ativo', NULL),
(4, '2026-03-29', '2026-04-05', 'dinheiro', 4, 4, 'ativo', NULL),
(5, '2026-03-30', '2026-04-07', 'pix', 5, 7, 'ativo', NULL);

COMMIT;
