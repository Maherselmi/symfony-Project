<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Form\BookType;
use App\Form\SearchBookType;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;


class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }

    #[Route('/book/add', name: 'add_book')]
    public function addBook(ManagerRegistry $manager, Request $request): Response
    {
        $em = $manager->getManager();

        $book = new Book();

        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {

            $book->setPublished(true);
            //Incrémentation nombre des livres pour chaque auteur
            $nb =  $book->getAuthor()->getNb_books() + 1;
            $book->getAuthor()->setNb_books($nb);
            
            $em->persist($book);
            $em->flush();

            return $this->redirectToRoute('list_book');
        }

        return $this->renderForm('book/addBook.html.twig', ['form' => $form]);
    }

    #[Route('/listBook', name: 'list_book')]
    public function listBook(BookRepository $bookrepository): Response
    {

        return $this->render('book/listBook.html.twig', [
            'books' => $bookrepository->findAll(),
        ]);
    }


    #[Route('/book/details/{id}', name: 'book_details')]
    public function show(BookRepository $bookrepository, $id): Response
    {
        return $this->render('book/showDetails.html.twig', [
            'book' => $bookrepository->find($id),
        ]);
    }

    #[Route('/book/edit/{id}', name: 'book_edit')]
    public function editBook(Request $request, ManagerRegistry $manager, $id, BookRepository $bookrepository): Response
    {
        $em = $manager->getManager();

        $book  = $bookrepository->find($id);
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em->persist($book);
            $em->flush();
            return $this->redirectToRoute('list_book');
        }

        return $this->renderForm('book/editBook.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    #[Route('/book/delete/{id}', name: 'book_delete')]
    public function deleteBook(Request $request, $id, ManagerRegistry $manager, BookRepository $bookRepository): Response
    {
        $em = $manager->getManager();
        $book = $bookRepository->find($id);

        $em->remove($book);
        $em->flush();

        return $this->redirectToRoute('list_book');
    }

    //Query Builder: Question 2
    #[Route('/book/list/search', name: 'app_book_search', methods: ['GET', 'POST'])]
    public function searchBookByRef(Request $request, BookRepository $bookRepository): Response
    {
        $book = new Book();
        $form = $this->createForm(SearchBookType::class, $book);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            return $this->render('book/listSearch.html.twig', [
                'books' => $bookRepository->showAllBooksByRef($book->getRef()),
                'f' => $form->createView()
            ]);
        }
        return $this->render('book/listSearch.html.twig', [
            'books' => $bookRepository->findAll(),
            'f' => $form->createView()
        ]);
    }

    //Query Builder: Question 3
    #[Route('/book/list/author', name: 'app_book_list_author', methods: ['GET'])]
    public function showOrderedBooksByAuthor(BookRepository $bookRepository): Response
    {
        return $this->render('book/listBookAuthor.html.twig', [
            'books' => $bookRepository->booksListByAuthors(),
        ]);
    }

    //Query Builder: Question 4
    #[Route('/book/list/QB', name: 'app_book_list_author_date', methods: ['GET'])]
    public function showBooksByDateAndNbBooks(BookRepository $bookRepository): Response
    {
        return $this->render('book/listBookDateNbBooks.html.twig', [
            'books' => $bookRepository->showBooksByDateAndNbBooks(10, '2023-01-01'),
        ]);
    }

    //Query Builder: Question 5
    #[Route('/book/list/author/update/{category}', name: 'app_book_list_author_update', methods: ['GET'])]
    public function updateBooksCategoryByAuthor($category, BookRepository $bookRepository): Response
    {
        $bookRepository->updateBooksCategoryByAuthor($category);
        return $this->render('book/listBookAuthor.html.twig', [
            'books' => $bookRepository->findAll(),
        ]);
    }

    ///Update with QueryBuilder
    #[Route('/book/UpdateQB')]
    function UpdateQB(BookRepository $repo)
    {
        $repo->UpdateQB();
        return $this->redirectToRoute('list_book');
    }

    //DQL: Question 1
    #[Route('/book/NbrCategory', name: 'book_Count')]
    function NbrCategory(BookRepository $repo)
    {
        $nbr = $repo->NbBookCategory();
        return $this->render('book/showNbrCategory.html.twig', [
            'nbr' => $nbr,
        ]);
    }

    //DQL: Question 2
    #[Route('/book/showBookTitle', name: 'book_showBookByTitle')]
    function showTitleBook(BookRepository $repo)
    {
        $books = $repo->findBookByPublicationDate();
        return $this->render('book/showBooks.html.twig', [
            'books' => $books,
        ]);
    }
}
