// costante che centralizza il percorso degli endpoint => se spostiamo la cartella cambiamo solo qui
// --> dopo ci si concatena il nome dell'endpoint specifico
const BASE_URL = "api/prodotti/";

// addEventListener("DOMContentLoaded") equivale a $(document).ready() di jQuery
// quando il doc è stato caricato + il DOM è pronto => carica il pannello
document.addEventListener("DOMContentLoaded", loadAdminDashboard);

// READ -------------------------------------------------------------------------
// carica e mostra l'elenco in una tabella 
function loadAdminDashboard() {
    
    // fetch invia una richiesta GET all'API read.php
    fetch(`${BASE_URL}read.php`)
        
        .then(res => res.json()) // conversione della risposta in oggetto javaSscript
        
        //riceve l'ogg e ci lavora
        .then(data => {
            console.log("Prodotti ricevuti:", data);
            
            // HTML della tabella 
            // backtip --> template literal = stringa su più righe
            let html = `
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                    <h3>Inventario Shop</h3>
                    <button class="btn btn-success btn-sm" onclick="showCreateProductForm()">+ Nuovo Prodotto</button>
                    </div>
                <p class="text-muted mb-0">Da qui puoi gestire il catalogo: <b>aggiungi</b> nuovi prodotti, <b>modifica</b> il prezzo di quelli esistenti o <b>eliminali</b>.</p>
                </div>
                
                <table class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Categoria</th>
                            <th>Prezzo</th>
                            <th>File Immagine</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>`;

            // scorriamo ogni prod --> per ongi p ricevuto aggiungiamo una riga della tabella con foreach
            // id="desc-${prod.id}" permette a showEditRow() di trovare esattamente quella cella e modificarla
            data.forEach(prod => {
                                
                html += `
                    <tr>
                        <td><strong>${prod.nome}</strong></td>
                        <td>${prod.categoria}</td>
                        <td class="text-success">€ ${parseFloat(prod.prezzo).toFixed(2)}</td>
                        <td><small>${prod.immagine || 'default.jpg'}</small></td>
                        <td id="desc-${prod.id}">
                            <button class="btn btn-warning btn-sm" onclick="showEditRow(${prod.id}, ${prod.prezzo})">€</button>
                            <button class="btn btn-danger btn-sm" onclick="deleteProduct(${prod.id})">X</button>
                        </td>
                    </tr>`;
            });

            html += `</tbody></table>`;
            
            // inseriamo l'HTML nel contenitore della pagina aggiornando il DOM
            // --> sostituiamo il contenuto con il risultato del ciclo
            document.getElementById("admin-container").innerHTML = html;
        });
}

// CREATE ------------------------------------------------------------------------------------
// mostra il form per inserire un new prod.
function showCreateProductForm() {
    
    // sostituiamo il contenuto del contenitore con il form di creazione
    document.getElementById("admin-container").innerHTML = `
        <h4>Nuovo Prodotto</h4>
        <form id="create-product-form" class="vstack gap-2">
            <input type="text" id="p-nome" class="form-control" placeholder="Nome" required>
            <select id="p-categoria" class="form-select">
                <option value="Integratori">Integratori</option>
                <option value="Attrezzatura">Attrezzatura</option>
                <option value="Abbigliamento">Abbigliamento</option>
            </select>
            <input type="number" step="0.01" id="p-prezzo" class="form-control" placeholder="Prezzo (€)" required>
            <input type="text" id="p-immagine" class="form-control" placeholder="Nome file immagine (es. prodotto.jpg)">
            <textarea id="p-descrizione" class="form-control" placeholder="Descrizione" rows="2" required></textarea>
            <div>
                <button type="submit" class="btn btn-primary btn-sm">Salva</button>
                <button type="button" class="btn btn-light btn-sm" onclick="loadAdminDashboard()">Annulla</button>
            </div>
        </form>`;

    // aggiungiamo il gestore dell'evento submit al form appena creato
    // addEventListener = chiamato dopo che il form è stato inserito nel DOM
    document.getElementById("create-product-form").addEventListener("submit", (e) => {
      
        //impedisce il comport. di default del form di ricaricare la pagina inviando i dati come form tradizionale
        e.preventDefault();
        
        // cerca l'elemento con id 'p-immagine' nel DOM
        // .value contiene il testo inserito dall'utente
        const img = document.getElementById("p-immagine").value.trim(); // rimozione spazi
        
        // DEBUG console
        console.log("Invio nuovo prodotto:", document.getElementById("p-nome").value);
        
        // inviamo i dati a create.php tramite AJAX
        fetch(`${BASE_URL}create.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            //JSON.stringify converte l'oggetto JavaScript in una stringa JSON inviata nel body della richiesta
            body: JSON.stringify({
                nome: document.getElementById("p-nome").value,
                categoria: document.getElementById("p-categoria").value,
                prezzo: document.getElementById("p-prezzo").value,
                descrizione: document.getElementById("p-descrizione").value,
                // operatore ternario --> se non c'è l'img si usa deafult.jpg
                immagine: img !== "" ? img : "default.jpg" 
            })
        })

        .then(res => res.json())
        .then(data => { 
            alert(data.message); // mostra il mex di risposta dell'API
            loadAdminDashboard(); // ricarica la tabella per mostrare il nuovo prodotto
        });
    });
}

// UPDATE --------------------------------------------------------------------------------------------
// modifica inline del prezzo 
function showEditRow(id, prezzo) {
    // modifica SOLO la cella con id="desc-{id}" --> non ricarica tutta la tabella
    // value="${prezzo}" precompila il campo con il prezzo attuale 
    document.getElementById(`desc-${id}`).innerHTML = `
        <div class="input-group input-group-sm">
            <input type="number" step="0.01" id="edit-prezzo-${id}" class="form-control" value="${prezzo}">
            <button class="btn btn-success" onclick="saveProductUpdate(${id})">Salva</button>
            <button class="btn btn-secondary" onclick="loadAdminDashboard()">Annulla</button>
        </div>`;
}

// salva il nuovo prezzo inviando una richiesta PUT all'API update.php
function saveProductUpdate(id) {
    fetch(`${BASE_URL}update.php`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ 
            id: id, 
            prezzo: document.getElementById(`edit-prezzo-${id}`).value })
    })
    .then(res => res.json())
    .then(data => { 
        alert(data.message); 
        loadAdminDashboard(); // ricarica la tab per vedere il prezzo aggiornato
    }); 
}

// DELETE -------------------------------------------------------------------------------------
// elimina un prodotto...
function deleteProduct(id) {
    //...dopo la CONFERMA dell'utente
    // confirm() mostra una finestra di dialogo nativa del browser
    if (confirm("Eliminare l'articolo?")) {
        // invia la richiesta delete all'API 
        fetch(`${BASE_URL}delete.php`, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
        })
        .then(res => res.json())
        .then(data => { 
            alert(data.message); 
            loadAdminDashboard(); // ricarica la tab aggiornata
        });
    }
}