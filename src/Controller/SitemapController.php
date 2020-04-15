<?php

namespace App\Controller;

use App\Entity\Blog;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SitemapController extends AbstractController
{
    /**
     * @Route("/sitemap.xml", name="sitemap", defaults={"_format"="xml"})
     */
    public function index(Request $request)
    {
        // Get the hostname from the url
        $hostname = $request->getSchemeAndHttpHost();

        // Initialize table URL
        $urls = [];

        // Add static URL
        $urls[] = ['loc' => $this->generateUrl('index')];
        $urls[] = ['loc' => $this->generateUrl('cv')];
        $urls[] = ['loc' => $this->generateUrl('certificates')];
        $urls[] = ['loc' => $this->generateUrl('about')];
        $urls[] = ['loc' => $this->generateUrl('blog')];
        $urls[] = ['loc' => $this->generateUrl('contact')];

        // Add dynamique URL from the blog
        foreach ($this->getDoctrine()->getRepository(Blog::class)->findAll() as $blog) {
            $data = $blog->getData();
            $images = '';

            if($data) {
                $images = [
                    'loc' => $this->getParameter('data_directory').$data->getName(), // URL to image
                    'title' => $blog->getTitle()    // Optional, text describing the image
                ];
            }

            $urls[] = [
                'loc' => $this->generateUrl('blog_details', [
                    'id' => $blog->getId(),
                ]),
                //'lastmod' => $blog->getUpdatedAt()->format('Y-m-d'),
                'image' => $images
            ];
        }

        // Xml response
        $response = new Response(
            $this->renderView('sitemap/index.html.twig', ['urls' => $urls,
                'hostname' => $hostname]),
            200
        );

        // Add headers
        $response->headers->set('Content-Type', 'text/xml');

        // Send response
        return $response;
    }
}
