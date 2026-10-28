-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Lug 04, 2026 alle 11:31
-- Versione del server: 10.4.32-MariaDB
-- Versione PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fit_tek_db`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `prodotti`
--

CREATE TABLE `prodotti` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descrizione` text NOT NULL,
  `prezzo` decimal(10,2) NOT NULL,
  `categoria` varchar(50) NOT NULL,
  `immagine` varchar(255) DEFAULT 'default.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `prodotti`
--

INSERT INTO `prodotti` (`id`, `nome`, `descrizione`, `prezzo`, `categoria`, `immagine`) VALUES
(5, 'Felpa sportiva', 'Felpa in cotone con cappuccio, comoda e adatta ad ogni attività.', 27.00, 'Abbigliamento', 'f1.jpg'),
(6, 'Maglietta', 'Maglietta termica aderente, ottima per i tuoi allenamenti.', 15.00, 'Abbigliamento', 'm1.jpg'),
(7, 'Borraccia', 'Borraccia termica in acciaio, tiene sia il freddo che il caldo.', 10.00, 'Attrezzatura', 'b1.jpg'),
(8, 'Guanti anti-calli', 'Guanti da allenamento anti-calli per proteggere le tue mani.', 9.99, 'Attrezzatura', 'g1.jpg');

-- --------------------------------------------------------

--
-- Struttura della tabella `utenti`
--

CREATE TABLE `utenti` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `ruolo` tinyint(1) NOT NULL DEFAULT 0,
  `creato_il` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `utenti`
--

INSERT INTO `utenti` (`id`, `username`, `email`, `password`, `ruolo`, `creato_il`) VALUES
(13, 'mario', 'mario@gmail.com', '$2y$10$I8M13VmvjQcHOrBiGAdtuudIv8Q7LhpGnRO9LGpX.nxKGFgFAIVRu', 0, '2026-06-30 10:43:01'),
(14, 'marti', 'marti@gmail.com', '$2y$10$i0jdp83z3Whngs/zybaKYuZOdp8GvVRSZrfAxMVeBgvwlrkE8Htce', 0, '2026-06-30 10:44:25'),
(15, 'admin', 'admin@fit-tek.it', '$2y$10$RGtp4QbZoM56cZ9ggPQoguIoZrbTvd5tW/vBjxu2j8urVxFb4FkJ6', 1, '2026-06-30 10:46:01');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `prodotti`
--
ALTER TABLE `prodotti`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `utenti`
--
ALTER TABLE `utenti`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `prodotti`
--
ALTER TABLE `prodotti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT per la tabella `utenti`
--
ALTER TABLE `utenti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
