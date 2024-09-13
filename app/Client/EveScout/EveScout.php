<?php


namespace Exodus4D\ESI\Client\EveScout;

use Exodus4D\ESI\Client;
use Exodus4D\ESI\Config\ConfigInterface;
use Exodus4D\ESI\Config\EveScout\Config;
use Exodus4D\ESI\Lib\RequestConfig;
use Exodus4D\ESI\Lib\WebClient;
use Exodus4D\ESI\Mapper\EveScout as Mapper;

class EveScout extends Client\AbstractApi implements EveScoutInterface {

    private function getHubConnectionsRequest(string hub) : RequestConfig {
        return new RequestConfig(
            WebClient::newRequest('GET', $this->getConfig()->getEndpoint(['wormholes', 'GET'])),
            $this->getRequestOptions('', null, [
                'system_name' => hub
            ]),
            function($body) : array {
                $connectionsData = [];
                if(!$body->error){
                    foreach((array)$body as $data){
                        $connectionsData['connections'][(int)$data->id] = (new Mapper\Connection($data))->getData();
                    }
                }else{
                    $connectionsData['error'] = $body->error;
                }

                return $connectionsData;
            }
        );
    }

    /**
     * @return RequestConfig
     */
    protected function getTheraConnectionsRequest() : RequestConfig {
        return getHubConnectionsRequest("thera");
    }

    /**
     * @return RequestConfig
     */
    protected function getTurnurConnectionsRequest() : RequestConfig {
        return getHubConnectionsRequest("turnur");
    }

    /**
     * @return ConfigInterface
     */
    protected function getConfig() : ConfigInterface {
        return ($this->config instanceof ConfigInterface) ? $this->config : $this->config = new Config();
    }
}
