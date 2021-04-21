<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Stmt\TryCatch;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class BlogController extends AbstractController
{
    #[Route('/blog', name: 'blog')]
    public function index(PostRepository $postRepo): Response
    {
        $posts = $postRepo->findAll();

        return $this->render('blog/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    /**
     * @Route("/", name="home")
     */
    public function home(){
        return $this->render('blog/home.html.twig', [
            'title' => 'Hello !'
        ]);
    }

    /**
     * @Route("/blog/new", name="blog_create")
     * @Route("/blog/{id}/edit", name="blog_edit")
     */
    public function new(Request $req, EntityManagerInterface $em, Post $post = null, SluggerInterface $slugger){

        if(!$post){
            $post = new Post;
        }
        $formPost = $this->createForm(PostType::class, $post);
        $formPost->handleRequest($req);

        if($formPost->isSubmitted() && $formPost->isValid()){

            /**
             * @var UploadedFile
             */
            $file = $formPost->get('image')->getData();

            if($file){
                $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $fileName = $slugger->slug($fileName);
                $fileName = $fileName . "-" . uniqid() . "." . $file->guessExtension();
                try {
                    $file->move($this->getParameter('image_directory'), $fileName);
                }
                catch(FileException $e) {

                }
                $post->setImage($fileName);
            }

            if(!$post->getId()){
                $post->setCreatedAt(new DateTime());    
            }
            $em->persist($post);
            $em->flush();
            return $this->redirectToRoute('blog_show', [
                'id' => $post->getId()
            ]);
        }

        return $this->render('blog/newPost.html.twig',[
            'form' => $formPost->createView()
        ]);
    }

    /**
     * @Route("/blog/{id}", name="blog_show")
     */
    public function show(Post $post){
        return $this->render('blog/show.html.twig', [
            'post' => $post
        ]);
    }
}
