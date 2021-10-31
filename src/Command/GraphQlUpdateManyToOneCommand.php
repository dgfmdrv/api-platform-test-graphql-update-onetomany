<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Psr\Log\LoggerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

use App\Service\GraphqlClient;
use App\Exception\GraphQLQueryException;

class GraphQlUpdateManyToOneCommand extends Command
{
    use LockableTrait;

    private LoggerInterface $logger;
    private ManagerRegistry $dManReg;
    private GraphqlClient $clientBackend;
    private ParameterBagInterface $params;


	public function __construct(
        LoggerInterface $logger, 
        ManagerRegistry $dManReg, 
        GraphqlClient $clientBackend,
        ParameterBagInterface $params
        )
    {
        parent::__construct();
        $this->logger = $logger;
        $this->dManReg = $dManReg;
        $this->clientBackend = $clientBackend;
        $this->params = $params;
	}
	
	protected function configure()
	{
		$this->setName('app:prueba');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{	
    $str="Inicio obtener vinculos pedidos nuevos";
    $output->writeln($str);  $this->logger->info($str);

    // 
    $str="Obteniendo ultimo punto sincronización";
    $output->writeln($str);  $this->logger->info($str);
    
    // lectura
    $query = <<<'GRAPHQL'
      query {
        cars {
          id
          nombre
          door {
            id
            nombre
          }
        }
      }
    GRAPHQL;
    
    $resultado=$this->clientBackend->query($query);    
    $output->writeln("query");
    print_r($resultado);
    
    // modificar
    $mutation = <<<'GRAPHQL'
      mutation ($id: ID!, $door: [String]) {
        updateCar(input: {id: $id, door: $door}) {
          car {
            id
            nombre
            door {
              id
              nombre
            }    
          }
        }
      }
    GRAPHQL;


    $resultado=$this->clientBackend->query($mutation, [
      'id' => '/api/cars/1', 
      'door' => ['/api/doors/2']
    ]);
    
    // 
    $output->writeln("mutation");
    print_r($resultado);    
    
    // if not released explicitly, Symfony releases the lock
    // automatically when the execution of the command ends
    $this->release();

    return 0;
  }
}
