-- ============================================================
-- enredados_wiki.sql
-- Base de datos para el Wiki de personajes de Enredados
-- Proyecto Academico - Aplicación web
-- ============================================================

CREATE DATABASE IF NOT EXISTS enredados_wiki
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_spanish2_ci;

USE enredados_wiki;

CREATE TABLE IF NOT EXISTS personajes (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  nombre      VARCHAR(60)  NOT NULL,
  rol         VARCHAR(40)  NOT NULL,
  aldea       VARCHAR(40),
  poder       INT,
  imagen      VARCHAR(255) NOT NULL,
  descripcion TEXT,
  ropa_cabello        VARCHAR(100),
  ropa_camiseta       VARCHAR(100),
  ropa_pantalon       VARCHAR(100),
  ropa_zapatos        VARCHAR(100),
  fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- Datos de ejemplo
-- Nota: Las imágenes usan placehold.co como fuente confiable.
-- Reemplazar por las URLs originales de Disney/Wikia cuando estén disponibles.

INSERT INTO personajes (nombre, rol, aldea, poder, imagen, descripcion,
  ropa_cabello, ropa_camiseta, ropa_pantalon, ropa_zapatos)
VALUES
('Rapunzel', 'Princesa', 'Corona', 75,
  'imagenes/galeria/rapunzel.jpg',
  'Princesa de Corona que escapó de su torre con sus 70 pies de cabello dorado.',
  'Dorado', 'Sin ropa', 'Sin ropa', 'Sin zapatos'),

('Flynn Rider', 'Héroe', 'Corona', 65,
  'imagenes/galeria/flynn.jpg',
  'Mercenario que se hace pasar por príncipe y se enamora de Rapunzel.',
  'Sin cabello', 'Camisa azul', 'Pantalones negros', 'Zapatos blancos'),

('Madre Gothel', 'Villana', 'Corona', 40,
  'imagenes/galeria/gothel.jpg',
  'La guardiana que mantuvo a Rapunzel encerrada en la torre.',
  'Gris', 'Sin ropa', 'Sin ropa', 'Sin zapatos'),

('Maximus', 'Caballo', 'Corona', 30,
  'imagenes/galeria/maximus.jpg',
  'El caballo valiente de la Guardia Real.',
  'Sin mancha', 'Sin ropa', 'Sin ropa', 'Sin zapatos'),

('Pascal', 'Compañero', 'Corona', 20,
  'imagenes/galeria/pascal.jpg',
  'El pequeño camaleón compañero de Rapunzel.',
  'Verde', 'Sin ropa', 'Sin ropa', 'Sin zapatos'),

('Lady Tremaine', 'Villana', 'Corona', 50,
  'imagenes/galeria/lady_tremaine.jpg',
  'La villana que se opuso al regreso de Rapunzel.',
  'Castaño', 'Sin ropa', 'Sin ropa', 'Sin zapatos'),

('Rey Frederic', 'Rey', 'Corona', 60,
  'imagenes/galeria/king_frederic.jpg',
  'El rey padre de Rapunzel y protector de Corona.',
  'Castaño', 'Traje negro', 'Pantalones azul marino', 'Zapatos negros'),

('El Patito Modosito', 'Amigo', 'Corona', 10,
  'imagenes/galeria/patito.jpg',
  'El pato blanco y adorable de Rapunzel.',
  'Blanco', 'Sin ropa', 'Sin ropa', 'Sin zapatos'),

('Los Hermanos Stabbington', 'Mercenarios', 'Corona', 35,
  'imagenes/galeria/stabbington.jpg',
  'Los hermanos Stabbington, mercenarios que acompañan a Flynn Rider.',
  'Sin cabello', 'Camisa roja', 'Pantalones azules', 'Zapatos grises'),

('Príncipe Felipe', 'Príncipe', 'Corona', 68,
  'https://placehold.co/600x400/6a1b9a/ffffff.png?text=Pr%C3%ADnceipe+Felipe',
  'El príncipe que busca a Rapunzel y Flynn Rider.',
  'Castaño', 'Traje azul', 'Pantalones grises', 'Zapatos negros'),

('Cassandra', 'Científica', 'Corona', 55,
  'https://placehold.co/600x400/2e7d32/ffffff.png?text=Cassandra',
  'La científica que ayudó a encontrar la flor de la torre.',
  'Gris', 'Ropa de laboratorio', 'Pantalones negros', 'Zapatos blancos'),

('Gurú', 'Maestro', 'Corona', 45,
  'https://placehold.co/600x400/6d4c41/ffffff.png?text=Gur%C3%BA',
  'El anciano mentor que guía a los personajes principales.',
  'Pelo blanco', 'Túnica azul', 'Pantalones marrones', 'Zapatos negros'),

('Zknak', 'Guardia', 'Corona', 15,
  'https://placehold.co/600x400/5d4037/ffffff.png?text=Zknak',
  'Un guardia de la corona que acompaña a la princesa.',
  'Rosa rojizo', 'Uniforme gris', 'Pantalones grises', 'Zapatos negros'),

('Flor de la Torre', 'Objeto mágico', 'Corona', 90,
  'https://placehold.co/600x400/00695c/ffffff.png?text=Flor+de+la+Torre',
  'La flor mágica que cura cualquier enfermedad y da belleza.',
  'N/A', 'N/A', 'N/A', 'N/A'),

('Rapunzel (niña)', 'Princesa', 'Corona', 55,
  'https://placehold.co/600x400/1565c0/ffffff.png?text=Rapunzel+Ni%C3%B1a',
  'Rapunzel como niña, antes de ser liberada de la torre.',
  'Dorado', 'Ropa azul', 'Pantalones azul', 'Zapatos negros'),

('Flynn Rider (joven)', 'Héroe', 'Corona', 50,
  'https://placehold.co/600x400/bf360c/ffffff.png?text=Flynn+Joven',
  'Flynn Rider en su juventud, antes de conocer a Rapunzel.',
  'Lucio', 'Camisa dorada', 'Pantalones negros', 'Zapatos marrones');
