<?php namespace Miclf\Allume;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Initialize a new Laravel project.
 *
 * @author Michaël Lecerf <michael@estsurinter.net>
 */
class LaravelCommand extends Command
{
    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this
            ->setName('laravel')
            ->setDescription('Initialize a new Laravel project.')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'The name of the project to initialize.',
                'miclf/awesome'
            )
        ;
    }

    /**
     * Execute the command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface    $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     *
     * @return null|int  null or 0 if everything went fine, an error code otherwise.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Check if the command is executed from the person’s home directory.
        if (!@chdir('./code')) {
           $output->writeln("<error>This has to be executed from the home directory</error>");
           exit(1);
        }

        // Determine the absolute path to this user’s home directory.
        $segments = explode('/', exec('pwd'));
        array_pop($segments);
        $homePath = implode('/', $segments);

        // Get the vendor and the project names from the CLI arguments.
        list($vendor, $name) = explode('/', $input->getArgument('name'));


        $helper = $this->getHelper('question');
    }
}
