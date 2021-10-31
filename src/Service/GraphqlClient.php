<?php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpClient\HttpOptions;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;

use App\Exception\GraphQLQueryException;

class GraphqlClient
{
    private LoggerInterface $logger;
    private HttpClientInterface $client;
    private ParameterBagInterface $params;
    private ?string $token;

    public function __construct(LoggerInterface $logger, HttpClientInterface $client, ParameterBagInterface $params)
    {
        $this->token = null;      
        $this->logger = $logger;
        $this->client = $client;
        $this->params = $params;
    }

    public function iniciarSesion() 
    {
        $url = $this->params->get('BACKEND_API_RESTAPI').'/authentication_token';
        $this->logger->debug("iniciarSesion. URL: $url");
        
        $response = $this->client->request(
            'POST', 
            $url, 
            [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'username' => $this->params->get('BACKEND_API_USERNAME'),
                    'password' => $this->params->get('BACKEND_API_PASSWORD'),
                ],
            ]);
        
        $responseData = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        if (is_null($responseData['token'])) {
            throw new \Exception("No se pudo iniciar sesion en $url usando {$this->params->get('BACKEND_API_USERNAME')}. HTTP Code: {$response->getStatusCode()}");
        }
        $this->token=$responseData['token'];

        $this->logger->debug("Sesion iniciada");
    }

    public function finSesion() 
    {
        $this->logger->debug('finSesion');
    }

    public function query(string $query, array $variables = []): array
    {
        $this->logger->debug('query');

        $options = (new HttpOptions())
            ->setJson(['query' => $query, 'variables' => $variables])
            ->setHeaders([
                'Content-Type' => 'application/json',
                'User-Agent' => 'Symfony GraphQL client'
            ])
        ;

        if ($this->token !== null) {
            $options->setAuthBearer($this->token);
        }
        
        $response = $this->client
            ->request('POST', $this->params->get('BACKEND_API_GRAPHQL'), $options->toArray())
            ->toArray();
        
        if (array_key_exists('errors', $response))
        {
            $this->logger->error('Error al lanzar query: ', $response);
            throw new GraphQLQueryException("Error al lanzar graphql query {$response['errors'][0]['message']}");
        }
        else
        {
            return $response;
        }
    }
    
    public function upload(string $query, array $variables = []): array
    {
        $this->logger->debug('upload');

        $data = [
            'operations' => '{ "query": '.json_encode($query).', "variables": '.json_encode($variables).' }',
            'map' => '{"file": ["variables.file"] }',
            'file' => DataPart::fromPath($variables['file'], iconv('UTF-8', 'ASCII//TRANSLIT', basename($variables['file']))),
        ];
        
        $formData = new FormDataPart($data);
        
        $response=$this->client->request('POST', $this->params->get('BACKEND_API_GRAPHQL'), [
            'headers' => $formData->getPreparedHeaders()->toArray(),
            'body' => $formData->bodyToString(),
            'auth_bearer' => $this->token
        ])->toArray();

        if (array_key_exists('errors', $response))
        {
            $this->logger->error('Error al lanzar query: ', $response);
            throw new GraphQLQueryException("Error al lanzar graphql query {$response['errors'][0]['message']}");
        }
        else
        {
            return $response;
        }
    }
}
