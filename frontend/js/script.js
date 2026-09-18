const API_URL = 'http://localhost/livraria/backend/';

async function carregarLivros() {

    const listaLivros = document.getElementById('lista-livros');

    try {

        const resposta = await fetch(API_URL);

        if (!resposta.ok) {
            throw new Error('Erro ao consultar a API.');
        }

        const livros = await resposta.json();

        listaLivros.innerHTML = '';

        livros.forEach(livro => {

            const card = document.createElement('div');

            card.classList.add('livro');

            card.innerHTML = `
                <img src="../arquivos/capas/${livro.capa}" alt="Capa de ${livro.titulo}">

                <h3>${livro.titulo}</h3>

                <p><strong>Autor:</strong> ${livro.autor}</p>

                <p><strong>Categoria:</strong> ${livro.categoria}</p>

                <p class="preco">R$ ${livro.preco}</p>

                <p><strong>Estoque:</strong> ${livro.estoque}</p>

                <div class="botoes-livro">
                    <button class="btn-editar">Editar</button>
                    <button class="btn-excluir">Excluir</button>
                </div>
            `;

            const btnEditar = card.querySelector('.btn-editar');

            btnEditar.addEventListener('click', () => {
                abrirFormularioEdicao(livro);
            });


            const btnExcluir = card.querySelector('.btn-excluir');

            btnExcluir.addEventListener('click', async () => {

            const confirmar = confirm(
                `Deseja realmente excluir o livro "${livro.titulo}"?`
            );

            if (!confirmar) {
                return;
            }

            try {

                const resposta = await fetch(`${API_URL}?id=${livro.id}`, {
                    method: 'DELETE'
                });

                const resultado = await resposta.json();

                if (!resposta.ok) {
                    throw new Error(resultado.erro || 'Erro ao excluir livro.');
                }

                alert(resultado.mensagem);

                carregarLivros();

            } catch (erro) {

                alert(erro.message);
                console.error(erro);
            }
        });

            listaLivros.appendChild(card);
        });

    } catch (erro) {

        listaLivros.innerHTML = `
            <p>Não foi possível carregar os livros.</p>
        `;

        console.error(erro);
    }
}

carregarLivros();

function abrirFormularioEdicao(livro) {

    formularioContainer.classList.remove('oculto');

    document.querySelector('#formulario-container h2').textContent = 'Editar livro';

    document.getElementById('titulo').value = livro.titulo;
    document.getElementById('autor').value = livro.autor;
    document.getElementById('categoria').value = livro.categoria;
    document.getElementById('preco').value = livro.preco;
    document.getElementById('estoque').value = livro.estoque;
    document.getElementById('capa').value = livro.capa;

    formularioLivro.dataset.id = livro.id;
}



const btnCadastrar = document.getElementById('btn-cadastrar');
const btnCancelar = document.getElementById('btn-cancelar');
const formularioContainer = document.getElementById('formulario-container');
const formularioLivro = document.getElementById('formulario-livro');

btnCadastrar.addEventListener('click', () => {
    formularioContainer.classList.remove('oculto');
});

btnCancelar.addEventListener('click', () => {
    formularioContainer.classList.add('oculto');
    formularioLivro.reset();
    formularioLivro.dataset.id = '';

    document.querySelector('#formulario-container h2').textContent = 'Cadastrar livro';
});

formularioLivro.addEventListener('submit', async (evento) => {

    evento.preventDefault();

    const id = formularioLivro.dataset.id;

    const livro = {
        titulo: document.getElementById('titulo').value,
        autor: document.getElementById('autor').value,
        categoria: document.getElementById('categoria').value,
        preco: parseFloat(document.getElementById('preco').value),
        estoque: parseInt(document.getElementById('estoque').value),
        capa: document.getElementById('capa').value
    };

    try {

        let resposta;

        if (id) {

            resposta = await fetch(`${API_URL}?id=${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(livro)
            });

        } else {

            resposta = await fetch(API_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(livro)
            });
        }

        const resultado = await resposta.json();

        if (!resposta.ok) {
            throw new Error(resultado.erro || 'Erro ao salvar livro.');
        }

        alert(resultado.mensagem);

        formularioLivro.reset();
        formularioLivro.dataset.id = '';
        formularioContainer.classList.add('oculto');

        document.querySelector('#formulario-container h2').textContent = 'Cadastrar livro';

        carregarLivros();

    } catch (erro) {

        alert(erro.message);
        console.error(erro);
    }
});