<?php

namespace Verschoof\BunqApiBundle\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

final class SetupBunqCommand extends Command
{
    /**
     * @var string
     */
    private $bunqLocation;

    /**
     * @param string $bunqLocation
     */
    public function __construct($bunqLocation)
    {
        parent::__construct(null);

        $this->bunqLocation = $bunqLocation;
    }


    protected function configure()
    {
        $this
            ->setName('bunq:setup')
            ->setDescription('Setup certificates and tokens')
            ->addOption('force', null, null, 'Remove existing files');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $certificateLocation = $this->bunqLocation . '/certificates';
        $tokenLocation       = $this->bunqLocation . '/tokens';

        $fileSystem = new Filesystem();

        $force = $input->getOption('force');

        if ($fileSystem->exists([$certificateLocation, $tokenLocation]) && !$force) {
            $output->writeln(sprintf('<error>Files already exist on location "%s"</error>', $this->bunqLocation));

            return;
        }

        $fileSystem->remove($certificateLocation);
        $fileSystem->remove($tokenLocation);

        $fileSystem->mkdir($certificateLocation);
        $fileSystem->mkdir($tokenLocation);

        $generateCertificate = new Process(
            'openssl genpkey -algorithm RSA -out private.pem -pkeyopt rsa_keygen_bits:2048 && openssl rsa -pubout -in private.pem -out public.pem',
            $certificateLocation
        );
        $generateCertificate->run();

        if (!$generateCertificate->isSuccessful()) {
            $output->writeln($generateCertificate->getErrorOutput());

            return;
        }

        $output->writeln('<info>Certificates succesfully installed</info>');
    }
}
