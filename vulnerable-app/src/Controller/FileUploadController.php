<?php declare(strict_types=1);

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FileUploadController extends AbstractController
{
    /**
     * @var ParameterBagInterface
     */
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    /**
     * @Route("/file-upload", methods={"GET", "POST"})
     */
    public function upload(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            /** @var UploadedFile $file */
            $file = $request->files->get('picture');
            $name = $file->getFilename() . '.' . $file->getClientOriginalExtension();
            $file->move($this->params->get('kernel.project_dir') . '/public/uploads', $name);

            return $this->render('uploaded.html.twig', ['file' => "uploads/$name"]);
        }

        return $this->render('upload.html.twig');
    }
}
